<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApprovalToken;
use App\Services\AutoAccountingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientPortalTokenController extends Controller
{
    protected AutoAccountingService $accountingService;

    public function __construct(AutoAccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function generateLink(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|string',
            'client_name' => 'required|string',
            'client_whatsapp' => 'required|string',
            'tenant_id' => 'required|string',
        ]);

        $token = ApprovalToken::create([
            'tenant_id' => $validated['tenant_id'],
            'project_id' => $validated['project_id'],
            'client_name' => $validated['client_name'],
            'client_whatsapp' => $validated['client_whatsapp'],
            'token' => Str::random(48),
            'approved_documents' => [],
            'expires_at' => now()->addDays(7),
        ]);

        $portalUrl = rtrim(config('app.frontend_url', 'http://localhost:3000'), '/')
            . '/client-portal/' . $token->token;

        return response()->json([
            'status' => 'success',
            'data' => [
                'token_id' => $token->id,
                'portal_url' => $portalUrl,
                'client_name' => $token->client_name,
                'expires_at' => $token->expires_at->toIso8601String(),
                'wa_message' => "Halo {$token->client_name}! Kami dari EventOS telah menyiapkan portal persetujuan digital untuk proyek Anda. Silakan akses tautan berikut untuk melihat dan menyetujui dokumen: {$portalUrl}",
            ],
        ]);
    }

    public function verify(string $token): JsonResponse
    {
        $approvalToken = ApprovalToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$approvalToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid atau sudah kedaluwarsa',
            ], 404);
        }

        $project = DB::table('projects')
            ->where('id', $approvalToken->project_id)
            ->first();

        $quotations = DB::table('quotations')
            ->where('tenant_id', $approvalToken->tenant_id)
            ->where('lead_id', $project?->lead_id)
            ->first();

        $quotationItems = collect();
        if ($quotations) {
            $quotationItems = DB::table('quotation_items')
                ->where('quotation_id', $quotations->id)
                ->get();
        }

        $invoices = DB::table('invoices')
            ->where('project_id', $approvalToken->project_id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $approvalToken->token,
                'project' => $project ? [
                    'id' => $project->id,
                    'title' => $project->title,
                    'client_name' => $project->client_name,
                    'event_date' => $project->event_date,
                    'venue_name' => $project->venue_name,
                    'progress_percentage' => $project->progress_percentage,
                ] : null,
                'quotation' => $quotations ? [
                    'id' => $quotations->id,
                    'title' => $quotations->title,
                    'grand_total' => (float) $quotations->grand_total,
                    'status' => $quotations->status,
                    'items' => $quotationItems->map(fn($i) => [
                        'id' => $i->id,
                        'category' => $i->category,
                        'title' => $i->title,
                        'vendor_name' => $i->vendor_name,
                        'price' => (float) $i->price,
                    ]),
                ] : null,
                'invoices' => $invoices->map(fn($i) => [
                    'id' => $i->id,
                    'termin_type' => $i->termin_type,
                    'amount' => (float) $i->amount,
                    'status' => $i->status,
                ]),
                'approved_documents' => $approvalToken->approved_documents,
                'client_name' => $approvalToken->client_name,
                'expires_at' => $approvalToken->expires_at->toIso8601String(),
            ],
        ]);
    }

    public function approveDocument(Request $request, string $token): JsonResponse
    {
        $approvalToken = ApprovalToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$approvalToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid atau sudah kedaluwarsa',
            ], 404);
        }

        $validated = $request->validate([
            'document_type' => 'required|string', // 3D_LAYOUT, RUNDOWN, QUOTATION
            'client_signature' => 'required|string',
            'grand_total' => 'nullable|numeric',
        ]);

        $docType = strtoupper($validated['document_type']);
        $approvedDocs = $approvalToken->approved_documents;

        if (in_array($docType, $approvedDocs)) {
            return response()->json([
                'status' => 'error',
                'message' => "Dokumen [{$docType}] sudah disetujui sebelumnya",
            ], 409);
        }

        $approvedDocs[] = $docType;
        $approvalToken->update(['approved_documents' => $approvedDocs]);

        $timestamp = now()->toIso8601String();

        $accountingData = null;
        if ($docType === 'QUOTATION' && !empty($validated['grand_total'])) {
            $accountingData = $this->accountingService->generateAutomatedInvoices(
                $approvalToken->tenant_id,
                $approvalToken->project_id,
                (float) $validated['grand_total']
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => "Pengesahan digital untuk [{$docType}] berhasil dicatat dengan E-Signature Timestamp.",
            'data' => [
                'token' => $approvalToken->token,
                'document_type' => $docType,
                'e_signature' => $validated['client_signature'],
                'approved_at' => $timestamp,
                'approved_documents' => $approvalToken->fresh()->approved_documents,
                'auto_accounting_triggered' => $accountingData,
            ],
        ]);
    }
}
