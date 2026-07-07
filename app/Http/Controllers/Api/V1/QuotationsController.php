<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuotationsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID', 'tenant-demo-uuid');
        $quotation = DB::table('quotations')
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$quotation) {
            return response()->json(['status' => 'success', 'data' => null, 'items' => []]);
        }

        $items = DB::table('quotation_items')
            ->where('quotation_id', $quotation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $quotation,
            'items' => $items,
        ]);
    }

    public function toggleItem(Request $request, string $itemId): JsonResponse
    {
        $item = DB::table('quotation_items')->where('id', $itemId)->first();
        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }

        $newSelected = !$item->is_selected;
        DB::table('quotation_items')
            ->where('id', $itemId)
            ->update(['is_selected' => $newSelected, 'updated_at' => now()]);

        $updatedItem = DB::table('quotation_items')->where('id', $itemId)->first();

        $quotationId = $item->quotation_id;
        $allItems = DB::table('quotation_items')->where('quotation_id', $quotationId)->where('is_selected', true)->get();
        $subtotal = $allItems->sum('price');

        DB::table('quotations')
            ->where('id', $quotationId)
            ->update(['subtotal' => $subtotal, 'updated_at' => now()]);

        return response()->json([
            'status' => 'success',
            'data' => $updatedItem,
            'subtotal' => $subtotal,
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quotation_id' => 'required|string',
            'category' => 'required|string',
            'title' => 'required|string',
            'vendor_name' => 'required|string',
            'price' => 'required|numeric',
            'is_optional' => 'boolean',
            'is_selected' => 'boolean',
        ]);

        $id = (string) Str::uuid();
        DB::table('quotation_items')->insert([
            'id' => $id,
            'quotation_id' => $validated['quotation_id'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'vendor_name' => $validated['vendor_name'],
            'price' => $validated['price'],
            'is_optional' => $validated['is_optional'] ?? false,
            'is_selected' => $validated['is_selected'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $item = DB::table('quotation_items')->where('id', $id)->first();

        $allItems = DB::table('quotation_items')->where('quotation_id', $validated['quotation_id'])->where('is_selected', true)->get();
        $subtotal = $allItems->sum('price');
        DB::table('quotations')->where('id', $validated['quotation_id'])->update(['subtotal' => $subtotal, 'updated_at' => now()]);

        return response()->json([
            'status' => 'success',
            'data' => $item,
            'subtotal' => $subtotal,
            'message' => 'Item added to quotation',
        ], 201);
    }

    public function lockQuotation(Request $request, string $id): JsonResponse
    {
        $quotation = DB::table('quotations')->where('id', $id)->first();
        if (!$quotation) {
            return response()->json(['status' => 'error', 'message' => 'Quotation not found'], 404);
        }

        if ($quotation->status === 'approved') {
            $project = DB::table('projects')
                ->where('tenant_id', $request->header('X-Tenant-ID', 'tenant-demo-uuid'))
                ->orderBy('created_at')
                ->first();

            $existingInvoices = collect();
            if ($project) {
                $existingInvoices = DB::table('invoices')
                    ->where('project_id', $project->id)
                    ->orderBy('created_at')
                    ->get()
                    ->map(fn($inv) => [
                        'id' => $inv->id,
                        'termin_type' => $inv->termin_type,
                        'amount' => (float) $inv->amount,
                        'status' => $inv->status,
                    ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Quotation already locked and approved',
                'data' => [
                    'quotation_id' => $id,
                    'status' => 'approved',
                    'grand_total' => (float) $quotation->grand_total,
                    'invoices' => $existingInvoices->toArray(),
                ],
            ]);
        }

        $tenantId = $request->header('X-Tenant-ID', 'tenant-demo-uuid');
        $grandTotal = (float) $quotation->grand_total > 0 ? (float) $quotation->grand_total : 180000000;
        $selectedItems = DB::table('quotation_items')
            ->where('quotation_id', $id)
            ->where('is_selected', true)
            ->get();
        $subtotal = $selectedItems->sum('price');

        $tax = round(($subtotal + 15000000) * 0.11);
        $grandTotalCalc = $subtotal + 15000000;

        DB::table('quotations')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $grandTotalCalc,
                'updated_at' => now(),
            ]);

        $firstProject = DB::table('projects')->where('tenant_id', $tenantId)->first();
        $projectId = $firstProject?->id ?? 'proj-1';

        $terms = [
            ['termin_type' => 'DP_30', 'amount' => round($grandTotalCalc * 0.3)],
            ['termin_type' => 'DP_50', 'amount' => round($grandTotalCalc * 0.5)],
            ['termin_type' => 'PELUNASAN', 'amount' => round($grandTotalCalc * 0.2)],
        ];

        $invoices = [];
        foreach ($terms as $i => $term) {
            $invId = (string) Str::uuid();
            DB::table('invoices')->insert([
                'id' => $invId,
                'tenant_id' => $tenantId,
                'project_id' => $projectId,
                'invoice_number' => 'INV/2026/' . strtoupper(substr(md5($invId), 0, 6)),
                'termin_type' => $term['termin_type'],
                'amount' => $term['amount'],
                'status' => $i === 0 ? 'paid' : 'unpaid',
                'payment_gateway_ref' => $i === 0 ? 'MIDTRANS-TRX-AUTO-' . strtoupper(substr(md5($invId), 0, 6)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $invoices[] = [
                'id' => $invId,
                'termin_type' => $term['termin_type'],
                'amount' => $term['amount'],
                'status' => $i === 0 ? 'paid' : 'unpaid',
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Quotation locked and invoices created',
            'data' => [
                'quotation_id' => $id,
                'status' => 'approved',
                'grand_total' => $grandTotalCalc,
                'invoices' => $invoices,
            ],
        ]);
    }

    public function exportQuotation(Request $request, string $id): JsonResponse
    {
        $quotation = DB::table('quotations')->where('id', $id)->first();
        if (!$quotation) {
            return response()->json(['status' => 'error', 'message' => 'Quotation not found'], 404);
        }

        $items = DB::table('quotation_items')
            ->where('quotation_id', $id)
            ->where('is_selected', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Quotation exported',
            'data' => [
                'title' => $quotation->title,
                'subtotal' => (float) $quotation->subtotal,
                'tax' => (float) $quotation->tax,
                'grand_total' => (float) $quotation->grand_total,
                'items' => $items->map(fn($i) => [
                    'category' => $i->category,
                    'title' => $i->title,
                    'vendor' => $i->vendor_name,
                    'price' => (float) $i->price,
                ]),
            ],
        ]);
    }

    public function sendQuotationWa(Request $request, string $id): JsonResponse
    {
        $quotation = DB::table('quotations')->where('id', $id)->first();
        if (!$quotation) {
            return response()->json(['status' => 'error', 'message' => 'Quotation not found'], 404);
        }

        $lead = DB::table('leads')->where('id', $quotation->lead_id)->first();

        DB::table('quotations')
            ->where('id', $id)
            ->update(['status' => 'sent', 'updated_at' => now()]);

        $waNumber = $lead?->whatsapp ?? '0812-8899-1234';
        $waLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $waNumber)
            . '?text=Halo%20' . urlencode($lead?->name ?? 'Kak')
            . '%2C%20berikut%20link%20penawaran%20acara%20Anda:%20'
            . urlencode(config('app.url') . '/quotation/' . $id);

        return response()->json([
            'status' => 'success',
            'message' => 'WhatsApp quotation link sent',
            'data' => [
                'wa_link' => $waLink,
                'wa_number' => $waNumber,
                'recipient' => $lead?->name ?? 'Unknown',
                'quotation_status' => 'sent',
            ],
        ]);
    }
}
