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
        // MP envia notificações IPN via Query Params e Webhooks via Body
        $data = $request->all();
        
        // Log para auditoria
        Log::info('Mercado Pago Webhook Received', [
            'query' => $request->query(),
            'body' => $request->json()->all(),
        ]);
        
        // Unificar dados (topic/id ou type/data.id)
        $topic = $request->input('type') ?? $request->input('topic');
        $id = $request->input('id') ?? ($request->input('data')['id'] ?? null);

        if (!$topic || !$id) {
            // Se não encontrou nos campos padrões, pode ser um teste de conexão
            return response()->json(['success' => true, 'message' => 'Connection test or empty data']);
        }

        // Delegar processamento ao serviço
        try {
            $this->mpService->processNotification([
                'type' => $topic,
                'data' => ['id' => $id]
            ]);
        } catch (\Exception $e) {
            Log::error('MercadoPagoWebhook: Erro ao processar notificação', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }

        return response()->json(['success' => true]);
    }
}
