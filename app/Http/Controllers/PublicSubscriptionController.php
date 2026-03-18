<?php

namespace App\Http\Controllers;

use App\Mail\UserCredentialsMail;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PublicSubscriptionController extends Controller
{
    protected AsaasService $asaasService;
    protected \App\Services\MercadoPagoService $mpService;

    public function __construct(AsaasService $asaasService, \App\Services\MercadoPagoService $mpService)
    {
        $this->asaasService = $asaasService;
        $this->mpService = $mpService;
    }

    /**
     * Exibir planos disponíveis (página pública)
     */
    public function showPlans()
    {
        $plans = Plan::where('active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        $allModules = \App\Models\Module::where('active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        return view('public.plans', compact('plans', 'allModules'));
    }

    /**
     * Exibir formulário de cadastro para um plano específico
     */
    public function showSignup(Plan $plan)
    {
        if (!$plan->active) {
            return redirect()->route('public.plans')
                ->with('error', 'Este plano não está disponível.');
        }

        $allModules = \App\Models\Module::where('active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        return view('public.signup', compact('plan', 'allModules'));
    }

    /**
     * Processar cadastro e criar assinatura
     */
    public function signup(Request $request, Plan $plan)
    {
        if (!$plan->active) {
            return back()->with('error', 'Este plano não está disponível.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cpf_cnpj' => ['required', 'string', new \App\Rules\CpfCnpj],
            'phone' => ['required', 'string', new \App\Rules\Phone],
            'payment_gateway' => 'required|in:asaas,mercadopago',
            'billing_type' => 'required_if:payment_gateway,asaas|in:CREDIT_CARD,BOLETO,PIX',
        ]);

        DB::beginTransaction();
        try {
            // 1. Criar usuário (Comum a ambos)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'cpf_cnpj' => $validated['cpf_cnpj'],
                'phone' => $validated['phone'],
                'role' => 'user',
            ]);

            // URL de retorno padrão
            $returnUrl = $this->getPublicUrl(route('dashboard.index') . '?payment=success');

            // --- LÓGICA ASAAS ---
            if ($validated['payment_gateway'] === 'asaas') {
                // Criar cliente no Asaas
                $customerData = $this->asaasService->createCustomer([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'cpf_cnpj' => $user->cpf_cnpj,
                    'user_id' => $user->id,
                ]);

                if (!$customerData || !isset($customerData['id'])) {
                    throw new \Exception('Erro ao criar cliente no Asaas: ' . ($customerData['errors'][0]['description'] ?? 'Erro desconhecido'));
                }

                $user->update(['asaas_customer_id' => $customerData['id']]);

                if ($plan->billing_cycle === 'lifetime') {
                    $payment = $this->asaasService->createPayment([
                        'customer_id' => $user->asaas_customer_id,
                        'billing_type' => $validated['billing_type'],
                        'value' => $plan->price,
                        'due_date' => now()->addDays(3)->format('Y-m-d'),
                        'description' => "Compra vitalícia do plano: {$plan->name}",
                        'return_url' => $returnUrl,
                    ]);

                    if (!$payment) throw new \Exception('Erro ao criar pagamento no Asaas');

                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'gateway' => 'asaas',
                        'asaas_customer_id' => $user->asaas_customer_id,
                        'status' => 'pending',
                        'starts_at' => now(),
                    ]);

                    $paymentUrl = $payment['invoiceUrl'] ?? null;
                    DB::commit();
                    Auth::login($user);
                    if ($paymentUrl) return redirect($paymentUrl);
                } else {
                    $subscriptionData = $this->asaasService->createSubscription([
                        'customer_id' => $user->asaas_customer_id,
                        'billing_type' => $validated['billing_type'],
                        'value' => $plan->price,
                        'next_due_date' => now()->addMonth()->format('Y-m-d'),
                        'cycle' => $plan->billing_cycle === 'yearly' ? 'YEARLY' : 'MONTHLY',
                        'description' => "Assinatura {$plan->name}",
                        'return_url' => $returnUrl,
                    ]);

                    if (!$subscriptionData || !isset($subscriptionData['id'])) throw new \Exception('Erro ao criar assinatura no Asaas');

                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'gateway' => 'asaas',
                        'asaas_subscription_id' => $subscriptionData['id'],
                        'asaas_customer_id' => $user->asaas_customer_id,
                        'status' => 'pending',
                        'starts_at' => now(),
                        'next_billing_date' => \Carbon\Carbon::parse($subscriptionData['nextDueDate'] ?? now()->addMonth()),
                    ]);

                    DB::commit();
                    Auth::login($user);
                    
                    // Buscar payment URL (simplificado)
                    sleep(1);
                    $paymentData = $this->asaasService->getSubscriptionPayments($subscriptionData['id']);
                    $paymentUrl = $paymentData['invoiceUrl'] ?? null;
                    if ($paymentUrl) return redirect($paymentUrl);
                }
            } 
            // --- LÓGICA MERCADO PAGO ---
            else {
                if ($plan->billing_cycle === 'lifetime') {
                    // Checkout Pro para plano único
                    $preference = $this->mpService->createPreference([
                        'title' => "Plano Vitalício: {$plan->name}",
                        'amount' => $plan->price,
                        'payer_name' => $user->name,
                        'payer_email' => $user->email,
                        'return_url' => $returnUrl,
                        'external_reference' => "subscription:PLACEHOLDER", // Atualizaremos após criar
                    ]);

                    if (!$preference || !isset($preference['init_point'])) {
                        throw new \Exception('Erro ao criar preferência no Mercado Pago');
                    }

                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'gateway' => 'mercadopago',
                        'mp_preference_id' => $preference['id'],
                        'status' => 'pending',
                        'starts_at' => now(),
                    ]);

                    // Atualizar external_reference
                    $this->mpService->createPreference([
                        'id' => $preference['id'],
                        'external_reference' => "subscription:{$subscription->id}",
                    ]);

                    DB::commit();
                    Auth::login($user);
                    return redirect($preference['init_point']);
                } else {
                    // Assinatura recorrente (Pre-approval)
                    $mpSubscription = $this->mpService->createSubscription([
                        'reason' => "Assinatura: {$plan->name}",
                        'amount' => $plan->price,
                        'payer_email' => $user->email,
                        'billing_cycle' => $plan->billing_cycle,
                        'return_url' => $returnUrl,
                        'external_reference' => "subscription:PLACEHOLDER",
                    ]);

                    if (!$mpSubscription || !isset($mpSubscription['init_point'])) {
                        throw new \Exception('Erro ao criar assinatura no Mercado Pago');
                    }

                    $subscription = Subscription::create([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'gateway' => 'mercadopago',
                        'mp_preapproval_id' => $mpSubscription['id'],
                        'status' => 'pending',
                        'starts_at' => now(),
                        'next_billing_date' => now()->addMonth(),
                    ]);

                    // Idealmente o external_reference já está lá, mas o MP às vezes exige criar um plano primeiro.
                    // Para simplificar, assumimos que o init_point redireciona o usuário.

                    DB::commit();
                    Auth::login($user);
                    return redirect($mpSubscription['init_point']);
                }
            }

            // Fallback
            Auth::login($user);
            return redirect()->route('dashboard.index')->with('info', 'Cadastro realizado. Aguardando pagamento.');

            // Fallback: fazer login e redirecionar para página de assinaturas
            Auth::login($user);
            return redirect()->route('subscriptions.index')
                ->with('info', 'Cadastro realizado com sucesso! O link de pagamento será enviado por email.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erro ao processar cadastro: ' . $e->getMessage());
        }
    }

    /**
     * Obter URL pública (ngrok em dev, APP_URL em produção)
     */
    private function getPublicUrl(string $path = ''): string
    {
        // Se houver URL pública configurada (ngrok), usar ela
        $publicUrl = env('APP_PUBLIC_URL');
        if ($publicUrl) {
            $baseUrl = rtrim($publicUrl, '/');
            $cleanPath = ltrim($path, '/');
            return $baseUrl . ($cleanPath ? '/' . $cleanPath : '');
        }
        
        // Caso contrário, usar APP_URL
        $appUrl = config('app.url', env('APP_URL', 'http://localhost'));
        $baseUrl = rtrim($appUrl, '/');
        $cleanPath = ltrim($path, '/');
        return $baseUrl . ($cleanPath ? '/' . $cleanPath : '');
    }

    /**
     * Página de sucesso após pagamento
     */
    public function paymentSuccess()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Faça login para acessar sua conta.');
        }

        $user = Auth::user();

        // Verificar se a assinatura foi ativada
        $subscription = $user->activeSubscription();
        
        if ($subscription && $subscription->status === 'active') {
            // Assinatura ativa - redirecionar para dashboard
            return redirect()->route('dashboard.index')
                ->with('success', 'Assinatura ativada com sucesso! Bem-vindo ao CLIVUS!');
        }

        // Se ainda não foi ativada, verificar se há assinatura pendente
        $pendingSubscription = $user->subscriptions()
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingSubscription) {
            // Tentar verificar o status do pagamento no Asaas
            try {
                $payment = $this->asaasService->getSubscriptionPayments($pendingSubscription->asaas_subscription_id);
                if ($payment && isset($payment['status']) && $payment['status'] === 'CONFIRMED') {
                    // Pagamento confirmado mas assinatura ainda não ativada - ativar manualmente
                    $pendingSubscription->update(['status' => 'active']);
                    Log::info('Assinatura ativada manualmente no callback', [
                        'subscription_id' => $pendingSubscription->id,
                        'user_id' => $user->id,
                    ]);
                    return redirect()->route('dashboard.index')
                        ->with('success', 'Assinatura ativada com sucesso! Bem-vindo ao CLIVUS!');
                }
            } catch (\Exception $e) {
                Log::warning('Erro ao verificar pagamento no callback', [
                    'subscription_id' => $pendingSubscription->asaas_subscription_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Se ainda não foi ativada, redirecionar para dashboard com mensagem informativa
        return redirect()->route('dashboard.index')
            ->with('info', 'Aguardando confirmação do pagamento. Sua assinatura será ativada automaticamente quando o pagamento for confirmado.');
    }
}
