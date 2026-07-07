<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\VendorStatusUpdated;
use App\Http\Controllers\Controller;
use App\Services\WhatsAppWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected WhatsAppWebhookService $waService;

    public function __construct(WhatsAppWebhookService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Webhook masuk dari WhatsApp saat vendor merespons tombol [Siap & Hadir]
     */
    public function whatsappCallback(Request $request): JsonResponse
    {
        $phone = $request->input('from', '081233334444');
        $buttonId = $request->input('button_id', 'CONFIRM_ATTENDANCE');
        $taskId = $request->input('task_id', 't-4');

        $result = $this->waService->processButtonReply($phone, $buttonId, $taskId);

        // Siarkan event realtime via Laravel Reverb ke antarmuka Next.js
        broadcast(new VendorStatusUpdated(
            'tenant-demo-uuid',
            'Grand Rose Decor',
            $result['status'],
            $result['broadcast_message']
        ))->toOthers();

        return response()->json([
            'status' => 'success',
            'reverb_broadcasted' => true,
            'data' => $result
        ]);
    }
}
