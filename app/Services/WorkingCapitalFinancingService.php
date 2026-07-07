<?php

namespace App\Services;

use App\Models\WorkingCapitalLoan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkingCapitalFinancingService
{
    /**
     * Inovasi Monopoli Tahap 2 #1: Embedded Working Capital Financing (EventOS PayLater)
     * Memberikan modal belanja bahan baku instan kepada vendor dengan jaminan arus kas Escrow.
     */
    public function disburseLoan(string $tenantId, string $vendorId, string $vendorName, string $escrowAccountId, float $contractHpp, float $loanRequested): array
    {
        return DB::transaction(function () use ($tenantId, $vendorId, $vendorName, $escrowAccountId, $contractHpp, $loanRequested) {
            $feePercentage = 1.50; // 1.5% flat platform fee
            $feeAmount = round($loanRequested * ($feePercentage / 100));
            $netDisbursed = $loanRequested - $feeAmount;

            $loan = WorkingCapitalLoan::create([
                'tenant_id' => $tenantId,
                'vendor_id' => $vendorId,
                'vendor_name' => $vendorName,
                'escrow_account_id' => $escrowAccountId,
                'contract_hpp_value' => $contractHpp,
                'loan_amount_requested' => $loanRequested,
                'platform_fee_percentage' => $feePercentage,
                'net_disbursement_idr' => $netDisbursed,
                'status' => 'active'
            ]);

            return [
                'success' => true,
                'loan_id' => $loan->id,
                'vendor_name' => $vendorName,
                'loan_requested' => $loanRequested,
                'platform_fee_idr' => $feeAmount,
                'net_disbursed_to_vendor' => $netDisbursed,
                'escrow_security_backed' => true,
                'message' => '💸 Pinjaman modal kerja cair dalam 15 menit ke rekening vendor! Pembayaran akan dipotong otomatis saat Escrow klien cair.'
            ];
        });
    }
}
