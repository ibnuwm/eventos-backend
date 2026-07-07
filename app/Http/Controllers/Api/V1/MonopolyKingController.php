<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WorkingCapitalFinancingService;
use App\Services\IoTAssetTrackingAndClaimService;
use App\Services\StageCommandShowCallerService;
use App\Services\AiComplianceAuditingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonopolyKingController extends Controller
{
    public function disburseCapital(Request $request, WorkingCapitalFinancingService $service): JsonResponse
    {
        $res = $service->disburseLoan(
            $request->header('X-Tenant-ID', 'tenant-demo-uuid'),
            $request->input('vendor_id', 'v-demo-1'),
            $request->input('vendor_name', 'Grand Rose Decor'),
            $request->input('escrow_account_id', 'escrow-demo-1'),
            (float) $request->input('contract_hpp_value', 40000000),
            (float) $request->input('loan_amount_requested', 25000000)
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    }

    public function scanAssetQr(Request $request, IoTAssetTrackingAndClaimService $service): JsonResponse
    {
        $res = $service->scanBarcode(
            $request->input('barcode', 'QR-LED-PAR-8899'),
            $request->input('project_id', 'proj-1'),
            $request->input('location_type', 'Loading Dock Hotel A')
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    }

    public function fileClaim(Request $request, IoTAssetTrackingAndClaimService $service): JsonResponse
    {
        $res = $service->fileDamageClaim(
            $request->input('asset_qr_id', 'qr-1'),
            $request->input('project_id', 'proj-1'),
            $request->input('photo_url', 'https://minio.eventos.id/claims/lens_broken.jpg')
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    }

    public function triggerStageCue(Request $request, StageCommandShowCallerService $service): JsonResponse
    {
        $res = $service->triggerCue(
            $request->input('project_id', 'proj-1'),
            $request->input('cue_number', 'CUE-14'),
            $request->input('moment_title', 'Grand Entrance & First Dance')
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    }

    public function auditPortfolio(Request $request, AiComplianceAuditingService $service): JsonResponse
    {
        $res = $service->auditVendor($request->input('vendor_name', 'Grand Rose Decor'));
        return response()->json(['status' => 'success', 'data' => $res]);
    }
}
