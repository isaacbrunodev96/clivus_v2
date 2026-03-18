<?php

namespace App\Http\Controllers;

use App\Models\UserModule;
use App\Models\Subscription;
use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentStatusController extends Controller
{
    protected AsaasService $asaasService;
    protected \App\Services\MercadoPagoService $mpService;

    public function __construct(AsaasService $asaasService, \App\Services\MercadoPagoService $mpService)
    {
        $this->asaasService = $asaasService;
        $this->mpService = $mpService;
    }

    /**
     * Verificar status do pagamento e redirecionar
     */
    public function checkStatus(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['redirect' => route('login')], 302);

        $moduleId = $request->query('module');
        $paymentId = $request->query('payment_id'); // Assas payment id
        $subscriptionId = $request->query('subscription_id'); // Assas subscription id or generic
        $mpPreferenceId = $request->query('preference_id');
        $mpPreapprovalId = $request->query('preapproval_id');

        // 1. Módulos
        if ($moduleId) {
            $userModule = UserModule::where('user_id', $user->id)
                ->where('module_id', $moduleId)
                ->latest()
                ->first();

            if ($userModule) {
                if ($userModule->status === 'active') {
                    return $this->redirectSuccess($request);
                }

                try {
                    if ($userModule->gateway === 'mercadopago' && $userModule->mp_payment_id) {
                        // MP costuma notificar via webhook, mas podemos tentar buscar
                        // (O MP não tem uma busca fácil por payment_id sem o token de acesso que já temos)
                        // Para simplificar, deixamos o webhook ativar
                    } elseif ($userModule->gateway === 'asaas' && $userModule->asaas_payment_id) {
                        $payment = $this->asaasService->getPayment($userModule->asaas_payment_id);
                        if ($payment && isset($payment['status']) && $payment['status'] === 'CONFIRMED') {
                            $userModule->update(['status' => 'active']);
                            return $this->redirectSuccess($request);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erro ao verificar pagamento de módulo', ['error' => $e->getMessage()]);
                }
            }
        }

        // 2. Assinaturas
        $subscription = null;
        if ($subscriptionId) {
            $subscription = Subscription::where('user_id', $user->id)
                ->where(function($q) use ($subscriptionId) {
                    $q->where('asaas_subscription_id', $subscriptionId)
                      ->orWhere('mp_preapproval_id', $subscriptionId)
                      ->orWhere('id', $subscriptionId);
                })->first();
        } else {
            $subscription = Subscription::where('user_id', $user->id)->where('status', 'pending')->latest()->first();
        }

        if ($subscription) {
            if ($subscription->status === 'active') return $this->redirectSuccess($request);

            try {
                if ($subscription->gateway === 'mercadopago') {
                    // MP logic
                } else {
                    $subscriptionData = $this->asaasService->getSubscription($subscription->asaas_subscription_id);
                    if ($subscriptionData && isset($subscriptionData['status']) && $subscriptionData['status'] === 'ACTIVE') {
                        $subscription->update(['status' => 'active']);
                        return $this->redirectSuccess($request);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Erro ao verificar assinatura', ['error' => $e->getMessage()]);
            }
        }

        if ($request->wantsJson() || $request->ajax()) return response()->json(['status' => 'pending'], 200);

        return redirect()->route('dashboard.index')->with('info', 'Aguardando confirmação do pagamento.');
    }

    private function redirectSuccess($request)
    {
        $url = route('dashboard.index') . '?payment=success';
        if ($request->wantsJson() || $request->ajax()) return response()->json(['redirect' => $url], 200);
        return redirect($url)->with('success', 'Pagamento confirmado!');
    }

    /**
     * Página de aguardando pagamento (com polling)
     */
    public function waiting(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $pendingPayment = session()->get('pending_payment');
        
        $moduleId = $request->query('module') ?? $pendingPayment['module_id'] ?? null;
        $subscriptionId = $request->query('subscription_id') ?? $pendingPayment['subscription_id'] ?? null;
        $paymentId = $request->query('payment_id') ?? $pendingPayment['payment_id'] ?? null;
        
        if ($pendingPayment) session()->forget('pending_payment');
        
        $redirectUrl = route('payment.status.check', [
            'module' => $moduleId,
            'subscription_id' => $subscriptionId,
            'payment_id' => $paymentId,
        ]);

        return view('payment.waiting', compact('redirectUrl', 'moduleId', 'subscriptionId'));
    }

    /**
     * Verificar pagamentos pendentes do usuário atual (para polling no dashboard)
     */
    public function checkPending(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $updated = false;
        $message = null;

        // Verificar módulos
        $pendingModules = UserModule::where('user_id', $user->id)->where('status', 'inactive')->get();
        foreach ($pendingModules as $userModule) {
            if ($userModule->gateway === 'asaas' && $userModule->asaas_payment_id) {
                try {
                    $payment = $this->asaasService->getPayment($userModule->asaas_payment_id);
                    if ($payment && isset($payment['status']) && $payment['status'] === 'CONFIRMED') {
                        $userModule->update(['status' => 'active']);
                        $updated = true;
                        $message = 'Módulo ativado com sucesso!';
                        break;
                    }
                } catch (\Exception $e) {}
            }
        }

        // Verificar assinaturas
        if (!$updated) {
            $pendingSubs = Subscription::where('user_id', $user->id)->where('status', 'pending')->get();
            foreach ($pendingSubs as $sub) {
                if ($sub->gateway === 'asaas' && $sub->asaas_subscription_id) {
                    try {
                        $data = $this->asaasService->getSubscription($sub->asaas_subscription_id);
                        if ($data && isset($data['status']) && $data['status'] === 'ACTIVE') {
                            $sub->update(['status' => 'active']);
                            $updated = true;
                            $message = 'Assinatura ativada com sucesso!';
                            break;
                        }
                    } catch (\Exception $e) {}
                }
            }
        }

        return response()->json([
            'updated' => $updated,
            'message' => $message,
            'has_pending' => $pendingModules->count() > 0 || Subscription::where('user_id', $user->id)->where('status', 'pending')->exists()
        ]);
    }
}

