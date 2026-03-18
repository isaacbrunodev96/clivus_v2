<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    protected MercadoPagoService $mpService;

    public function __construct(MercadoPagoService $mpService)
    {
        $this->mpService = $mpService;
    }

    /**
     * Receber notificações do Mercado Pago
     */
    public function handle(Request $request)
    {
        $data = $request->all();
        
        Log::info('Mercado Pago Webhook Received', $data);

        // Validar token se configurado
        $expectedToken = config('services.mercadopago.webhook_token');
        if ($expectedToken && $request->header('x-mercadopago-token') !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->mpService->processNotification($data);

        return response()->json(['success' => true]);
    }
}
