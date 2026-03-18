<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\UserModule;
use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ModuleStoreController extends Controller
{
    protected AsaasService $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Exibe a loja de módulos disponíveis
     */
    public function index()
    {
        $user = Auth::user();
        
        // Módulos disponíveis para compra ou ativação (preço > 0 ou gratuitos)
        $availableModules = Module::where('active', true)
            ->where(function($query) {
                $query->where('price', '>', 0)
                      ->orWhere('billing_cycle', 'free');
            })
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        // Módulos já comprados ou ativos pelo usuário
        $purchasedModuleIds = $user->userModules()
            ->where('status', 'active')
            ->pluck('module_id')
            ->toArray();

        // Módulos incluídos no plano atual
        $subscription = $user->activeSubscription();
        $planModuleSlugs = [];
        if ($subscription && $subscription->plan) {
            $planModuleSlugs = $subscription->plan->allowed_modules ?? [];
        }

        // Filtrar módulos já incluídos no plano
        $availableModules = $availableModules->filter(function($module) use ($purchasedModuleIds, $planModuleSlugs) {
            return !in_array($module->id, $purchasedModuleIds) && 
                   !in_array($module->slug, $planModuleSlugs);
        });

        // Módulos comprados pelo usuário
        $userModules = $user->userModules()
            ->with('module')
            ->where('status', 'active')
            ->get();

        return view('modules.store', compact('availableModules', 'userModules'));
    }

    /**
     * Processa a compra ou ativação de um módulo
     */
    public function purchase(Request $request, Module $module)
    {
        $user = Auth::user();

        // Verificar se já possui o módulo
        $existingModule = $user->userModules()
            ->where('module_id', $module->id)
            ->where('status', 'active')
            ->first();

        if ($existingModule) {
            return redirect()->route('modules.store')
                ->with('error', 'Você já possui este módulo ativo.');
        }

        // Se o módulo for gratuito, ativar imediatamente
        if ($module->billing_cycle === 'free' || $module->price <= 0) {
            UserModule::updateOrCreate(
                ['user_id' => $user->id, 'module_id' => $module->id],
                [
                    'status' => 'active',
                    'price_paid' => 0,
                    'purchased_at' => now(),
                    'expires_at' => null,
                ]
            );

            return redirect()->route('modules.store')
                ->with('success', "Módulo {$module->name} ativado com sucesso!");
        }

        // billing_type supports PIX, CREDIT_CARD, BOLETO
        $validated = $request->validate([
            'billing_type' => 'required|in:PIX,CREDIT_CARD,BOLETO',
        ]);

        // Verificar se está incluído no plano
        $subscriptionPlan = $user->activeSubscription();
        if ($subscriptionPlan && $subscriptionPlan->plan) {
            $allowedModules = $subscriptionPlan->plan->allowed_modules ?? [];
            if (in_array($module->slug, $allowedModules)) {
                return redirect()->route('modules.store')
                    ->with('error', 'Este módulo já está incluído no seu plano.');
            }
        }

        // Verificar se o usuário tem asaas_customer_id
        if (!$user->asaas_customer_id) {
            return redirect()->route('modules.store')
                ->with('error', 'Você precisa ter um plano ativo para comprar módulos adicionais.');
        }

        try {
            $returnUrl = $this->getPublicUrl(route('modules.payment.callback', ['module' => $module->id]));
            $paymentUrl = null;
            $asaasId = null;
            $isSubscription = in_array($module->billing_cycle, ['monthly', 'yearly']);

            if ($isSubscription) {
                // Criar assinatura recorrente (Mensal ou Anual)
                $cycle = $module->billing_cycle === 'yearly' ? 'YEARLY' : 'MONTHLY';
                $asaasResult = $this->asaasService->createSubscription([
                    'customer_id' => $user->asaas_customer_id,
                    'billing_type' => $validated['billing_type'],
                    'value' => $module->price,
                    'next_due_date' => now()->addDays(3)->format('Y-m-d'),
                    'cycle' => $cycle,
                    'description' => "Assinatura {$module->billing_cycle} do módulo: {$module->name}",
                    'return_url' => $returnUrl,
                ]);
                
                if ($asaasResult && isset($asaasResult['id'])) {
                    $asaasId = $asaasResult['id'];
                    // Obter o primeiro pagamento da assinatura
                    sleep(1);
                    $paymentData = $this->asaasService->getSubscriptionPayments($asaasId);
                    if ($paymentData && isset($paymentData['invoiceUrl'])) {
                        $paymentUrl = $paymentData['invoiceUrl'];
                    }
                }
            } else {
                // Pagamento Vitalício (Evento Único)
                $asaasResult = $this->asaasService->createPayment([
                    'customer_id' => $user->asaas_customer_id,
                    'billing_type' => $validated['billing_type'],
                    'value' => $module->price,
                    'due_date' => now()->addDays(3)->format('Y-m-d'),
                    'description' => "Compra vitalícia do módulo: {$module->name}",
                    'return_url' => $returnUrl,
                ]);

                if ($asaasResult && isset($asaasResult['id'])) {
                    $asaasId = $asaasResult['id'];
                    $paymentUrl = $asaasResult['invoiceUrl'] ?? $asaasResult['checkoutUrl'] ?? null;
                }
            }

            if (!$asaasId) {
                throw new \Exception('Erro ao gerar cobrança no Asaas');
            }

            // Registrar UserModule
            UserModule::updateOrCreate(
                ['user_id' => $user->id, 'module_id' => $module->id],
                [
                    'subscription_id' => $isSubscription ? $asaasId : null,
                    'asaas_payment_id' => !$isSubscription ? $asaasId : null,
                    'price_paid' => $module->price,
                    'status' => 'inactive',
                    'purchased_at' => now(),
                    'gateway' => 'asaas',
                ]
            );

            if ($paymentUrl) {
                return redirect($paymentUrl);
            }

            return redirect()->route('modules.store')
                ->with('info', 'Cobrança gerada. O módulo será ativado após a confirmação do pagamento.');

        } catch (\Exception $e) {
            Log::error('Erro ao processar compra de módulo: ' . $e->getMessage());
            return redirect()->route('modules.store')
                ->with('error', 'Erro ao processar compra: ' . $e->getMessage());
        }
    }

    /**
     * Callback após pagamento do módulo
     */
    public function paymentCallback(Request $request)
    {
        $moduleId = $request->query('module');
        $user = Auth::user();

        if (!$moduleId || !$user) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Parâmetros inválidos.');
        }

        // Verificar se o módulo foi ativado
        $userModule = UserModule::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->where('status', 'active')
            ->first();

        if ($userModule) {
            // Módulo ativado com sucesso - redirecionar para dashboard
            return redirect()->route('dashboard.index')
                ->with('success', 'Módulo ativado com sucesso!');
        }

        // Se ainda não foi ativado, verificar se o pagamento está pendente
        $userModule = UserModule::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->where('status', 'inactive')
            ->first();

        if ($userModule && $userModule->asaas_payment_id) {
            // Tentar verificar o status do pagamento no Asaas
            try {
                $payment = $this->asaasService->getPayment($userModule->asaas_payment_id);
                if ($payment && isset($payment['status']) && $payment['status'] === 'CONFIRMED') {
                    // Pagamento confirmado mas módulo ainda não ativado - ativar manualmente
                    $userModule->update(['status' => 'active']);
                    Log::info('Módulo ativado manualmente no callback', [
                        'user_module_id' => $userModule->id,
                        'module_id' => $moduleId,
                        'user_id' => $user->id,
                    ]);
                    return redirect()->route('dashboard.index')
                        ->with('success', 'Módulo ativado com sucesso!');
                }
            } catch (\Exception $e) {
                Log::warning('Erro ao verificar pagamento no callback', [
                    'payment_id' => $userModule->asaas_payment_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Se ainda não foi ativado, redirecionar para dashboard com mensagem informativa
        return redirect()->route('dashboard.index')
            ->with('info', 'Aguardando confirmação do pagamento. O módulo será ativado automaticamente quando o pagamento for confirmado.');
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
}
