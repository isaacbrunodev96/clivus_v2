<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    private string $accessToken;
    private bool $sandbox;
    private string $baseUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token') ?? env('MERCADOPAGO_ACCESS_TOKEN', '');
    }

    /**
     * Criar preferência de pagamento (Checkout Pro) - Para pagamentos únicos
     */
    public function createPreference(array $data): ?array
    {
        if (empty($this->accessToken)) {
            Log::error('MercadoPagoService: Access Token não configurado.');
            return null;
        }

        $response = Http::withToken($this->accessToken)
            ->post("{$this->baseUrl}/checkout/preferences", [
                'items' => [
                    [
                        'title' => $data['title'],
                        'quantity' => 1,
                        'unit_price' => (float) $data['amount'],
                        'currency_id' => 'BRL',
                    ]
                ],
                'payer' => [
                    'name' => $data['payer_name'] ?? '',
                    'email' => $data['payer_email'] ?? '',
                ],
                'back_urls' => [
                    'success' => $data['return_url'] ?? route('dashboard.index'),
                    'failure' => $data['return_url'] ?? route('dashboard.index'),
                    'pending' => $data['return_url'] ?? route('dashboard.index'),
                ],
                'auto_return' => 'approved',
                'notification_url' => route('webhook.mercadopago'),
                'external_reference' => $data['external_reference'] ?? '',
            ]);

        if ($response->failed()) {
            Log::error('MercadoPagoService: Erro ao criar preferência', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return null;
        }

        return $response->json();
    }

    /**
     * Criar Assinatura (Pre-approval) - Para planos recorrentes
     */
    public function createSubscription(array $data): ?array
    {
        if (empty($this->accessToken)) {
            Log::error('MercadoPagoService: Access Token não configurado.');
            return null;
        }

        $response = Http::withToken($this->accessToken)
            ->post("{$this->baseUrl}/preapproval", [
                'reason' => $data['reason'],
                'external_reference' => $data['external_reference'],
                'payer_email' => $data['payer_email'],
                'auto_recurring' => [
                    'frequency' => 1,
                    'frequency_type' => $data['billing_cycle'] === 'yearly' ? 'months' : 'months', // Simplificado para meses
                    'transaction_amount' => (float) $data['amount'],
                    'currency_id' => 'BRL',
                ],
                'back_url' => $data['return_url'] ?? route('dashboard.index'),
                'status' => 'authorized',
            ]);

        if ($response->failed()) {
            Log::error('MercadoPagoService: Erro ao criar assinatura', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return null;
        }

        return $response->json();
    }

    /**
     * Processar notificação de IPN ou Webhook
     */
    public function processNotification(array $data): void
    {
        Log::info('MercadoPagoService: Processing Webhook', $data);

        $type = $data['type'] ?? $data['topic'] ?? null;
        $id = $data['data']['id'] ?? $data['id'] ?? null;

        if (!$type || !$id) {
            Log::warning('MercadoPagoService: Webhook sem tipo ou ID', $data);
            return;
        }

        if ($type === 'payment') {
            $this->handlePaymentNotification($id);
        } elseif ($type === 'subscription' || $type === 'preapproval') {
            $this->handleSubscriptionNotification($id);
        }
    }

    /**
     * Buscar detalhes do pagamento e atualizar banco
     */
    private function handlePaymentNotification(string $paymentId): void
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/v1/payments/{$paymentId}");

        if ($response->failed()) {
            Log::error("MercadoPagoService: Erro ao buscar pagamento {$paymentId}");
            return;
        }

        $payment = $response->json();
        $status = $payment['status'] ?? '';
        $externalReference = $payment['external_reference'] ?? '';

        Log::info("MercadoPagoService: Payment {$paymentId} status: {$status}", [
            'external_reference' => $externalReference
        ]);

        if ($status === 'approved') {
            // Lógica para ativar plano vitalício ou módulo
            // Parse external_reference: "type:id"
            if (str_contains($externalReference, ':')) {
                [$refType, $refId] = explode(':', $externalReference);
                
                if ($refType === 'subscription') {
                    $subscription = \App\Models\Subscription::find($refId);
                    if ($subscription) {
                        $subscription->update(['status' => 'active']);
                        Log::info("MercadoPagoService: Local subscription {$refId} activated via payment.");
                    }
                } elseif ($refType === 'module') {
                    $userModule = \App\Models\UserModule::find($refId);
                    if ($userModule) {
                        $userModule->update(['status' => 'active']);
                        Log::info("MercadoPagoService: Local user module {$refId} activated.");
                    }
                }
            }
        }
    }

    /**
     * Buscar detalhes da assinatura e atualizar banco
     */
    private function handleSubscriptionNotification(string $preapprovalId): void
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/preapproval/{$preapprovalId}");

        if ($response->failed()) {
            Log::error("MercadoPagoService: Erro ao buscar assinatura {$preapprovalId}");
            return;
        }

        $subscriptionData = $response->json();
        $status = $subscriptionData['status'] ?? '';
        $externalReference = $subscriptionData['external_reference'] ?? '';

        if ($status === 'authorized' || $status === 'active') {
            if (str_contains($externalReference, ':')) {
                [$refType, $refId] = explode(':', $externalReference);
                if ($refType === 'subscription') {
                    $subscription = \App\Models\Subscription::find($refId);
                    if ($subscription) {
                        $subscription->update([
                            'status' => 'active',
                            'asaas_subscription_id' => $preapprovalId // Guardar ID do MP no campo genérico ou criar um novo
                        ]);
                        Log::info("MercadoPagoService: Local subscription {$refId} activated via preapproval.");
                    }
                }
            }
        }
    }
}
