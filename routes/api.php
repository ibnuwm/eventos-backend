<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\AiController;
use App\Http\Controllers\Api\V1\MarketplaceController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Controllers\Api\V1\ClientPortalController;
use App\Http\Controllers\Api\V1\InnovationController;
use App\Http\Controllers\Api\V1\MonopolyKingController;
use App\Services\AiProjectManagerService;
use App\Services\KnowledgeBaseRagService;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes - Vendor Event OS (EventOS.id) Backend v12.0
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 1. Executive Dashboard Overview
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // 2. Generative AI Assistant Copilot
    Route::post('/ai/copilot', [AiController::class, 'generate']);

    // 3. Improvement #1: AI Project Manager
    Route::post('/ai/project-manager/generate', function (Request $request, AiProjectManagerService $service) {
        $plan = $service->generateProjectPlan(
            $request->header('X-Tenant-ID', 'tenant-demo-uuid'),
            $request->input('title', 'Royal Wedding Custom AI'),
            $request->input('client_name', 'Anisa & Budi'),
            $request->input('event_type', 'Grand Ballroom Wedding'),
            $request->input('event_date', '2026-12-12'),
            (int) $request->input('pax_count', 800)
        );
        return response()->json(['status' => 'success', 'data' => $plan]);
    });

    // 4. Improvement #8: Knowledge Base AI
    Route::post('/ai/knowledge-base/query', function (Request $request, KnowledgeBaseRagService $rag) {
        $res = $rag->queryKnowledgeBase(
            $request->header('X-Tenant-ID', 'tenant-demo-uuid'),
            $request->input('question', 'Apa aturan denda keterlambatan dismantling hotel?')
        );
        return response()->json(['status' => 'success', 'data' => $res]);
    });

    // 5. Vendor Marketplace Search
    Route::get('/marketplace/vendors', [MarketplaceController::class, 'search']);

    // 6. Cloud Storage Asset Upload
    Route::post('/files/upload', [FileController::class, 'upload']);

    // 7. Client Portal E-Signature
    Route::post('/client-portal/approve', [ClientPortalController::class, 'approveDocument']);

    // 8. WhatsApp-Native Webhook
    Route::post('/webhooks/whatsapp', [WebhookController::class, 'whatsappCallback']);

    // =========================================================================
    // STAGE 1 CATEGORY KING 6 INNOVATIONS
    // =========================================================================
    Route::prefix('innovations')->group(function () {
        Route::post('/escrow/lock', [InnovationController::class, 'lockEscrow']);
        Route::post('/escrow/release', [InnovationController::class, 'releaseEscrow']);
        Route::post('/surge-pricing/evaluate', [InnovationController::class, 'evaluateSurge']);
        Route::post('/technical-rider/compile', [InnovationController::class, 'generateRider']);
        Route::get('/supply-chain/pools', [InnovationController::class, 'getPools']);
        Route::post('/supply-chain/join', [InnovationController::class, 'joinPool']);
    });

    // =========================================================================
    // STAGE 2 CATEGORY MONOPOLY KING 5 BREAKTHROUGHS
    // =========================================================================
    Route::prefix('monopoly')->group(function () {
        Route::post('/capital/disburse', [MonopolyKingController::class, 'disburseCapital']);
        Route::post('/iot/scan-qr', [MonopolyKingController::class, 'scanAssetQr']);
        Route::post('/iot/damage-claim', [MonopolyKingController::class, 'fileClaim']);
        Route::post('/stage-command/trigger-cue', [MonopolyKingController::class, 'triggerStageCue']);
        Route::post('/compliance/audit-portfolio', [MonopolyKingController::class, 'auditPortfolio']);
    });
});
