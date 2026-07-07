<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookService
{
    /**
     * Menangani webhook dari Meta Cloud API / Qontak saat vendor menekan tombol interaktif WA
     */
    public function processButtonReply(string $vendorPhone, string $buttonPayload, string $taskId): array
    {
        Log::info("WhatsApp Webhook Received from {$vendorPhone} with payload [{$buttonPayload}]");

        $statusMap = [
            'CONFIRM_ATTENDANCE' => ['status' => 'confirmed', 'message' => '✔ Vendor Konfirmasi Hadir'],
            'START_LOADING'      => ['status' => 'loading',   'message' => '🚀 Sedang Pengerjaan di Lokasi'],
            'FINISH_TASK'        => ['status' => 'done',      'message' => '🎉 Instalasi Selesai 100%']
        ];

        $newStatus = $statusMap[$buttonPayload] ?? ['status' => 'pending', 'message' => 'Pending'];

        // Update task di database MySQL
        if ($taskId && DB::table('project_tasks')->where('id', $taskId)->exists()) {
            DB::table('project_tasks')->where('id', $taskId)->update([
                'is_completed' => ($newStatus['status'] === 'done'),
                'updated_at' => now()
            ]);
        }

        // Catat log percakapan sistem
        return [
            'processed' => true,
            'phone' => $vendorPhone,
            'status' => $newStatus['status'],
            'broadcast_message' => $newStatus['message'],
            'timestamp' => now()->toIso8601String()
        ];
    }
}
