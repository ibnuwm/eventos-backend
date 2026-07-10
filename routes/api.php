<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\AiController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\GuestRsvpController;
use App\Http\Controllers\Api\V1\TicketingController;
use App\Http\Controllers\Api\V1\VendorAuthController;
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
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\MidtransController;
use App\Http\Controllers\Api\V1\VendorAnalyticsController;
use App\Http\Controllers\Api\V1\WishlistController;
use App\Http\Controllers\Api\V1\InspirationBoardController;
use App\Http\Controllers\Api\V1\VendorReviewController;
use App\Http\Controllers\Api\V1\VirtualExpoController;
use App\Http\Controllers\Api\V1\ForumController;
use App\Http\Controllers\Api\V1\UgcGalleryController;
use App\Http\Controllers\Api\V1\AdCampaignController;
use App\Http\Controllers\Api\V1\PremiumProfileController;
use App\Http\Controllers\Api\V1\SponsoredContentController;
use App\Http\Controllers\Api\V1\ApiKeyController;
use App\Http\Controllers\Api\V1\TenantRegistrationController;
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

    // Payment Gateway (#2)
    Route::post('/payments/invoice', [PaymentController::class, 'createInvoicePayment']);
    Route::post('/payments/simulate', [PaymentController::class, 'simulatePayment']);
    Route::get('/payments/invoice/{invoiceId}', [PaymentController::class, 'getPaymentStatus']);

    // Guest RSVP (#3)
    Route::get('/guests', [GuestRsvpController::class, 'index']);
    Route::post('/guests', [GuestRsvpController::class, 'store']);
    Route::get('/rsvp/{token}', [GuestRsvpController::class, 'rsvpVerify']);
    Route::post('/rsvp/{token}', [GuestRsvpController::class, 'rsvpConfirm']);

    // Ticketing (#5) - Public endpoints
    Route::get('/events', [TicketingController::class, 'listEvents']);
    Route::get('/events/{eventId}', [TicketingController::class, 'getEvent']);
    Route::post('/tickets/order', [TicketingController::class, 'createOrder']);
    Route::get('/tickets/verify/{qrToken}', [TicketingController::class, 'verifyTicket']);
    Route::post('/tickets/checkin/{qrToken}', [TicketingController::class, 'checkIn']);

    // Vendor Self-Service (#6)
    Route::post('/vendor/login', [VendorAuthController::class, 'login']);
    Route::get('/vendor/dashboard', [VendorAuthController::class, 'dashboard']);

    // =========================================================================
    // PHASE 1: BLOG CMS, MIDTRANS, VENDOR ANALYTICS
    // =========================================================================
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/blog/{slug}', [BlogController::class, 'show']);
    Route::post('/blog', [BlogController::class, 'store']);
    Route::put('/blog/{id}', [BlogController::class, 'update']);
    Route::delete('/blog/{id}', [BlogController::class, 'destroy']);

    // Midtrans Payment Gateway
    Route::post('/midtrans/charge', [MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/notification', [MidtransController::class, 'notificationHandler']);
    Route::get('/midtrans/status/{transactionId}', [MidtransController::class, 'getStatus']);

    // Vendor Analytics
    Route::post('/vendor-analytics/track-view', [VendorAnalyticsController::class, 'trackView']);
    Route::get('/vendor-analytics/stats/{vendorId}', [VendorAnalyticsController::class, 'getVendorStats']);
    Route::post('/vendor-analytics/lead', [VendorAnalyticsController::class, 'createLead']);
    Route::get('/vendor-analytics/leads/{vendorId}', [VendorAnalyticsController::class, 'getLeads']);

    // =========================================================================
    // PHASE 2: CONSUMER MARKETPLACE, REVIEWS, EXPO
    // =========================================================================
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
    Route::get('/wishlist/session/{sessionId}', [WishlistController::class, 'getBySession']);

    Route::get('/inspiration-boards', [InspirationBoardController::class, 'index']);
    Route::post('/inspiration-boards', [InspirationBoardController::class, 'store']);
    Route::put('/inspiration-boards/{id}', [InspirationBoardController::class, 'update']);
    Route::post('/inspiration-boards/{id}/items', [InspirationBoardController::class, 'addItem']);
    Route::delete('/inspiration-boards/{id}', [InspirationBoardController::class, 'destroy']);

    Route::get('/reviews/{vendorId}', [VendorReviewController::class, 'index']);
    Route::post('/reviews', [VendorReviewController::class, 'store']);
    Route::get('/reviews/{vendorId}/average', [VendorReviewController::class, 'getAverage']);

    Route::get('/virtual-expos', [VirtualExpoController::class, 'index']);
    Route::get('/virtual-expos/{id}', [VirtualExpoController::class, 'show']);
    Route::post('/virtual-expos', [VirtualExpoController::class, 'store']);
    Route::post('/virtual-expos/booths', [VirtualExpoController::class, 'registerBooth']);
    Route::get('/virtual-expos/{expoId}/booths', [VirtualExpoController::class, 'getBooths']);
    Route::post('/virtual-expos/booths/{boothId}/visit', [VirtualExpoController::class, 'trackVisitor']);

    // =========================================================================
    // PHASE 3: COMMUNITY, FORUM, UGC, REGISTRATION
    // =========================================================================
    Route::get('/forum/topics', [ForumController::class, 'index']);
    Route::get('/forum/topics/{id}', [ForumController::class, 'show']);
    Route::post('/forum/topics', [ForumController::class, 'store']);
    Route::post('/forum/topics/{topicId}/replies', [ForumController::class, 'reply']);
    Route::get('/forum/categories', [ForumController::class, 'getCategories']);

    Route::get('/ugc-gallery', [UgcGalleryController::class, 'index']);
    Route::post('/ugc-gallery', [UgcGalleryController::class, 'store']);
    Route::post('/ugc-gallery/{id}/approve', [UgcGalleryController::class, 'approve']);
    Route::get('/ugc-gallery/vendor/{vendorId}', [UgcGalleryController::class, 'getByVendor']);

    Route::post('/tenant/register', [TenantRegistrationController::class, 'register']);
    Route::post('/tenant/verify/{id}', [TenantRegistrationController::class, 'verify']);

    // =========================================================================
    // PHASE 4: MONETIZATION - ADS, PREMIUM, SPONSORED, API KEYS
    // =========================================================================
    Route::get('/ad-campaigns/{vendorId}', [AdCampaignController::class, 'index']);
    Route::post('/ad-campaigns', [AdCampaignController::class, 'store']);
    Route::post('/ad-campaigns/{campaignId}/impression', [AdCampaignController::class, 'recordImpression']);
    Route::post('/ad-campaigns/{campaignId}/click', [AdCampaignController::class, 'recordClick']);
    Route::patch('/ad-campaigns/{id}/status', [AdCampaignController::class, 'updateStatus']);

    Route::get('/premium-profiles', [PremiumProfileController::class, 'index']);
    Route::post('/premium-profiles', [PremiumProfileController::class, 'store']);
    Route::get('/premium-profiles/featured', [PremiumProfileController::class, 'getFeatured']);
    Route::get('/premium-profiles/vendor/{vendorId}/badge', [PremiumProfileController::class, 'verifyBadge']);

    Route::get('/sponsored-content', [SponsoredContentController::class, 'index']);
    Route::post('/sponsored-content', [SponsoredContentController::class, 'store']);
    Route::get('/sponsored-content/{id}', [SponsoredContentController::class, 'show']);

    Route::get('/api-keys', [ApiKeyController::class, 'index']);
    Route::post('/api-keys', [ApiKeyController::class, 'store']);
    Route::post('/api-keys/{id}/revoke', [ApiKeyController::class, 'revoke']);
});
