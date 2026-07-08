<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class PaymentController extends Controller {
    public function createInvoicePayment(Request $request): JsonResponse {
        $validated = $request->validate([
            'invoice_id' => 'required|string',
            'payment_method' => 'required|string', // VA, QRIS, CC
        ]);
        $invoice = DB::table('invoices')->where('id', $validated['invoice_id'])->first();
        if (!$invoice) return response()->json(['status' => 'error', 'message' => 'Invoice tidak ditemukan'], 404);
        if ($invoice->status === 'paid') return response()->json(['status' => 'error', 'message' => 'Invoice sudah dibayar'], 409);
        $trxId = 'TRX-' . strtoupper(Str::random(10));
        $vaNumber = '988' . rand(1000000000, 9999999999);
        DB::table('invoices')->where('id', $validated['invoice_id'])->update([
            'payment_gateway_ref' => $trxId,
            'updated_at' => now(),
        ]);
        return response()->json([
            'status' => 'success',
            'data' => [
                'transaction_id' => $trxId,
                'invoice_id' => $invoice->id,
                'amount' => (float) $invoice->amount,
                'payment_method' => $validated['payment_method'],
                'va_number' => $vaNumber,
                'status' => 'pending',
                'expires_at' => now()->addHours(24)->toIso8601String(),
            ],
        ]);
    }
    public function simulatePayment(Request $request): JsonResponse {
        $validated = $request->validate(['transaction_id' => 'required|string']);
        $invoice = DB::table('invoices')->where('payment_gateway_ref', $validated['transaction_id'])->first();
        if (!$invoice) return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);
        DB::table('invoices')->where('id', $invoice->id)->update([
            'status' => 'paid', 'updated_at' => now(),
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran berhasil!',
            'data' => ['invoice_id' => $invoice->id, 'status' => 'paid'],
        ]);
    }
    public function getPaymentStatus(string $invoiceId): JsonResponse {
        $invoice = DB::table('invoices')->where('id', $invoiceId)->first();
        if (!$invoice) return response()->json(['status' => 'error', 'message' => 'Invoice tidak ditemukan'], 404);
        return response()->json([
            'status' => 'success',
            'data' => [
                'invoice_id' => $invoice->id,
                'status' => $invoice->status,
                'payment_gateway_ref' => $invoice->payment_gateway_ref,
                'amount' => (float) $invoice->amount,
                'termin_type' => $invoice->termin_type,
            ],
        ]);
    }
}
