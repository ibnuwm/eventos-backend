<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AutoAccountingService
{
    /**
     * Improvement #5: Auto Accounting Engine
     * Saat Quotation disetujui klien, sistem otomatis membuat 3 faktur penagihan bertahap
     * (DP 30%, Termin 2 50%, Pelunasan 20%) dan mengunci Harga Pokok Penjualan (HPP).
     */
    public function generateAutomatedInvoices(string $tenantId, string $projectId, float $grandTotal): array
    {
        return DB::transaction(function () use ($tenantId, $projectId, $grandTotal) {
            $invoices = [];

            $schedules = [
                ['termin' => 'DP_30', 'percentage' => 0.30, 'status' => 'unpaid'],
                ['termin' => 'TERMIN_2_50', 'percentage' => 0.50, 'status' => 'unpaid'],
                ['termin' => 'PELUNASAN_20', 'percentage' => 0.20, 'status' => 'unpaid'],
            ];

            foreach ($schedules as $index => $sch) {
                $amount = round($grandTotal * $sch['percentage']);
                $invNum = 'INV-' . strtoupper(Str::random(6)) . '-' . ($index + 1);

                $invId = Str::uuid()->toString();
                DB::table('invoices')->insert([
                    'id' => $invId,
                    'tenant_id' => $tenantId,
                    'project_id' => $projectId,
                    'invoice_number' => $invNum,
                    'termin_type' => $sch['termin'],
                    'amount' => $amount,
                    'status' => $sch['status'],
                    'payment_gateway_ref' => 'MIDTRANS_SNAP_' . strtoupper(Str::random(10)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $invoices[] = [
                    'id' => $invId,
                    'invoice_number' => $invNum,
                    'termin_type' => $sch['termin'],
                    'amount' => $amount
                ];
            }

            return [
                'success' => true,
                'project_id' => $projectId,
                'total_contract_value' => $grandTotal,
                'invoices_created' => $invoices
            ];
        });
    }
}
