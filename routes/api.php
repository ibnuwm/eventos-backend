<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\AiController;
use App\Http\Controllers\Api\V1\MarketplaceController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Controllers\Api\V1\ClientPortalController;
use App\Http\Controllers\Api\V1\ClientPortalTokenController;
use App\Http\Controllers\Api\V1\VendorStorefrontController;
use App\Http\Controllers\Api\V1\InnovationController;
use App\Http\Controllers\Api\V1\MonopolyKingController;
use App\Http\Controllers\Api\V1\LeadsController;
use App\Http\Controllers\Api\V1\ProjectsController;
use App\Http\Controllers\Api\V1\QuotationsController;
use App\Http\Controllers\Api\V1\RundownController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\StaffController;
use App\Services\AiProjectManagerService;
use App\Services\KnowledgeBaseRagService;
use Illuminate\Http\Request;

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

    // 4. Knowledge Base AI
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

    // 7b. Client Portal Token-Based Access (Magic Link)
    Route::post('/client-portal/generate-link', [ClientPortalTokenController::class, 'generateLink']);
    Route::get('/client-portal/token/{token}/verify', [ClientPortalTokenController::class, 'verify']);
    Route::post('/client-portal/token/{token}/approve', [ClientPortalTokenController::class, 'approveDocument']);

    // 8. WhatsApp-Native Webhook
    Route::post('/webhooks/whatsapp', [WebhookController::class, 'whatsappCallback']);

    // =========================================================================
    // PUBLIC STOREFRONT (No X-Tenant-ID required - public access)
    // =========================================================================
    Route::get('/storefront/vendors', [VendorStorefrontController::class, 'index']);
    Route::get('/storefront/vendors/{id}', [VendorStorefrontController::class, 'show']);

    // =========================================================================
    // CRUD OPERASIONAL MODUL
    // =========================================================================

    // Tenant info (subscription tier)
    Route::get('/tenant', function (\Illuminate\Http\Request $request) {
        $tenantId = $request->header('X-Tenant-ID', 'tenant-demo-uuid');
        $tenant = \Illuminate\Support\Facades\DB::table('tenants')->where('id', $tenantId)->first();
        if (!$tenant) {
            return response()->json(['status' => 'error', 'message' => 'Tenant not found'], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $tenant->id,
                'company_name' => $tenant->company_name,
                'domain_slug' => $tenant->domain_slug,
                'subscription_tier' => $tenant->subscription_tier,
            ],
        ]);
    });

    // Leads
    Route::get('/leads', [LeadsController::class, 'index']);
    Route::patch('/leads/{id}/status', [LeadsController::class, 'updateStatus']);

    // Projects & Tasks (subscription-gated: basic max 3)
    Route::get('/projects', [ProjectsController::class, 'index'])->middleware('subscription:basic');
    Route::patch('/projects/tasks/{taskId}/toggle', [ProjectsController::class, 'toggleTask'])->middleware('subscription:basic');

    // Quotations
    Route::get('/quotations', [QuotationsController::class, 'index']);
    Route::patch('/quotation-items/{itemId}/toggle', [QuotationsController::class, 'toggleItem']);
    Route::post('/quotation-items', [QuotationsController::class, 'addItem']);
    Route::post('/quotations/{id}/lock', [QuotationsController::class, 'lockQuotation']);
    Route::get('/quotations/{id}/export', [QuotationsController::class, 'exportQuotation']);
    Route::post('/quotations/{id}/send-wa', [QuotationsController::class, 'sendQuotationWa']);

    // Rundown
    Route::get('/rundown-items', [RundownController::class, 'index']);
    Route::post('/rundown-items', [RundownController::class, 'store']);

    // Chat
    Route::get('/messages', [ChatController::class, 'index']);
    Route::post('/messages', [ChatController::class, 'store']);

    // Inventory
    Route::get('/inventory-items', [InventoryController::class, 'index']);

    // Staff
    Route::get('/staff-crews', [StaffController::class, 'index']);

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
    // STAGE 2 MONOPOLY KING 5 BREAKTHROUGHS
    // =========================================================================
    Route::prefix('monopoly')->group(function () {
        Route::post('/capital/disburse', [MonopolyKingController::class, 'disburseCapital']);
        Route::post('/iot/scan-qr', [MonopolyKingController::class, 'scanAssetQr']);
        Route::post('/iot/damage-claim', [MonopolyKingController::class, 'fileClaim']);
        Route::post('/stage-command/trigger-cue', [MonopolyKingController::class, 'triggerStageCue']);
        Route::post('/compliance/audit-portfolio', [MonopolyKingController::class, 'auditPortfolio']);
    });
});
