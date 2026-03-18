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
        $this->accessToken = config('services.mercadopago.access_token');
        // Mercado Pago handles sandbox via different credentials/test users
    }

    /**
     * Criar preferência de pagamento (Checkout Pro)
     */
    public function createPreference(array $data): ?array
    {
        // Placeholder for future implementation
        Log::info('MercadoPagoService: createPreference placeholder called', $data);
        return null;
    }

    /**
     * Processar notificação de IPN ou Webhook
     */
    public function processNotification(array $data): void
    {
        Log::info('MercadoPagoService: Webhook received', $data);
        // Implementation for processing Mercado Pago events
    }
}
