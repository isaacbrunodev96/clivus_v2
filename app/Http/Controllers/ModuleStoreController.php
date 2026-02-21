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
        
        // Módulos disponíveis para compra
        $availableModules = Module::where('active', true)
            ->where('price', '>', 0)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        // Módulos já comprados pelo usuário
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
     * Processa a compra de um módulo
     */
    public function purchase(Request $request, Module $module)
    {
        // billing_type supports PIX, CREDIT_CARD, BOLETO
        $validated = $request->validate([
            'billing_type' => 'required|in:PIX,CREDIT_CARD,BOLETO',
        ]);

        $user = Auth::user();

        // Verificar se já possui o módulo
        $existingModule = $user->userModules()
            ->where('module_id', $module->id)
            ->where('status', 'active')
            ->first();

        if ($existingModule) {
            return redirect()->route('modules.store')
                ->with('error', 'Você já possui este módulo.');
        }

        // Verificar se está incluído no plano
        $subscription = $user->activeSubscription();
        if ($subscription && $subscription->plan) {
            $allowedModules = $subscription->plan->allowed_modules ?? [];
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

        // Criar assinatura mensal no Asaas (módulos são cobrados por mensalidade)
        try {
            // URL de retorno após pagamento - página de aguardando que verifica status
            $returnUrl = $this->getPublicUrl(route('modules.payment.callback', ['module' => $module->id]));

            // Verificar se já existe um UserModule pendente para este módulo
            $existingUserModule = UserModule::where('user_id', $user->id)
                ->where('module_id', $module->id)
                ->first();

            if ($existingUserModule && $existingUserModule->status === 'active') {
                return redirect()->route('modules.store')
                    ->with('error', 'Você já possui este módulo.');
            }

            // Criar assinatura recorrente no Asaas
            $subscription = $this->asaasService->createSubscription([
                'customer_id' => $user->asaas_customer_id,
                'billing_type' => $validated['billing_type'],
                'value' => $module->price,
                'next_due_date' => now()->addMonth()->format('Y-m-d'),
                'cycle' => 'MONTHLY',
                'description' => "Assinatura mensal do módulo: {$module->name}",
                'subscription_id' => null,
                'return_url' => $returnUrl,
            ]);

            if (!$subscription || !isset($subscription['id'])) {
                throw new \Exception('Erro ao criar assinatura do módulo no Asaas');
            }

            // Criar ou atualizar registro do módulo do usuário associando a assinatura
            if ($existingUserModule) {
                $existingUserModule->update([
                    'subscription_id' => $subscription['id'] ?? null,
                    'price_paid' => $module->price,
                    'asaas_payment_id' => $subscription['id'] ?? null,
                    'status' => 'inactive',
                    'purchased_at' => now(),
                ]);
            } else {
                UserModule::create([
                    'user_id' => $user->id,
                    'module_id' => $module->id,
                    'subscription_id' => $subscription['id'] ?? null,
                    'price_paid' => $module->price,
                    'asaas_payment_id' => $subscription['id'] ?? null,
                    'status' => 'inactive',
                    'purchased_at' => now(),
                ]);
            }

            // A assinatura cria automaticamente um pagamento inicial — tentar obter URL
            $paymentUrl = null;
            sleep(1);
            $paymentData = $this->asaasService->getSubscriptionPayments($subscription['id']);
            if ($paymentData && isset($paymentData['invoiceUrl'])) {
                $paymentUrl = $paymentData['invoiceUrl'];
            } elseif ($paymentData && isset($paymentData['invoiceNumber'])) {
                $baseUrl = config('services.asaas.sandbox', true) 
                    ? 'https://sandbox.asaas.com'
                    : 'https://www.asaas.com';
                $paymentUrl = "{$baseUrl}/i/{$paymentData['invoiceNumber']}";
            }

            if ($paymentUrl) {
                session()->put('pending_payment', [
                    'type' => 'module_subscription',
                    'module_id' => $module->id,
                    'subscription_id' => $subscription['id'] ?? null,
                ]);
                return redirect($paymentUrl);
            }

            return redirect()->route('modules.store')
                ->with('info', 'Assinatura criada. A ativação será feita quando o pagamento for confirmado.');
        } catch (\Exception $e) {
            Log::error('Erro ao processar assinatura de módulo: ' . $e->getMessage(), [
                'module_id' => $module->id,
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('modules.store')
                ->with('error', 'Erro ao processar assinatura do módulo: ' . $e->getMessage());
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
