<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\EscrowPaymentEngineService;
use App\Services\TechnicalRiderGeneratorService;
use App\Services\SupplyChainPoolingService;
use App\Services\SurgePricingAndWeatherGuardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InnovationController extends Controller
{
    // 1. Escrow Split Payment Endpoints
    public function lockEscrow(Request $request, EscrowPaymentEngineService $service): JsonResponse
    {
        $res = $service->lockClientPaymentInEscrow(
            $request->header('X-Tenant-ID', 'tenant-demo-uuid'),
            $request->input('project_id', 'proj-demo-uuid'),
            (float) $request->input('total_payment', 90000000)
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    }

    public function releaseEscrow(Request $request, EscrowPaymentEngineService $service): JsonResponse
    {
        $res = $service->releaseEscrowToVendor($request->input('escrow_account_id'));
        return response()->json(['status' => 'success', 'data' => $res]);
    }

    // 2. Technical Rider PDF Compile
    public function generateRider(Request $request, TechnicalRiderGeneratorService $service): JsonResponse
    {
        $res = $service->compileTechnicalRider(
            $request->header('X-Tenant-ID', 'tenant-demo-uuid'),
            $request->input('project_id', 'proj-demo-uuid'),
            $request->input('selected_vendors', ['ProSound Audio 10.000W', 'Grand Rose Decor 15m', 'LED Screen P3'])
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    }

    // 3. B2B Supply Chain Pooling
    public function getPools(SupplyChainPoolingService $service): JsonResponse
    {
        return response()->json(['status' => 'success', 'data' => $service->getActivePools()]);
    }

    public function joinPool(Request $request, SupplyChainPoolingService $service): JsonResponse
    {
        $res = $service->joinGroupBuying(
            $request->header('X-Tenant-ID', 'tenant-demo-uuid'),
            $request->input('pool_id'),
            $request->input('vendor_name', 'Grand Rose Decor'),
            (int) $request->input('order_qty', 2000)
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    }

    // 4. Surge Pricing & Weather Guard AI
    public function evaluateSurge(Request $request, SurgePricingAndWeatherGuardService $service): JsonResponse
    {
        $res = $service->evaluateDateAndVenue(
            $request->input('event_date', '2026-08-14'),
            $request->input('venue_type', 'Outdoor Garden Poolside')
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    }
}
