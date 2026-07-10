<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MidtransController extends Controller
{
    public function createTransaction(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'invoice_id' => 'required|string',
                'payment_method' => 'required|string|in:VA,QRIS,CC',
            ]);

            $invoice = DB::table('invoices')->where('id', $validated['invoice_id'])->first();
            if (!$invoice) {
                return response()->json(['status' => 'error', 'message' => 'Invoice not found'], 404);
            }
            if ($invoice->status === 'paid') {
                return response()->json(['status' => 'error', 'message' => 'Invoice already paid'], 409);
            }

            $transactionId = 'MID-' . strtoupper(Str::random(10));
            $vaNumber = '988' . rand(1000000000, 9999999999);
            $qrCode = 'https://api.midtrans.dev/qr/' . $transactionId;
            $paymentUrl = 'https://app.midtrans.com/payment/' . $transactionId;

            DB::table('invoices')->where('id', $validated['invoice_id'])->update([
                'payment_gateway_ref' => $transactionId,
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transaction_id' => $transactionId,
                    'invoice_id' => $invoice->id,
                    'amount' => (float) $invoice->amount,
                    'payment_method' => $validated['payment_method'],
                    'payment_url' => $paymentUrl,
                    'va_number' => $validated['payment_method'] === 'VA' ? $vaNumber : null,
                    'qr_code' => $validated['payment_method'] === 'QRIS' ? $qrCode : null,
                    'status' => 'pending',
                    'expires_at' => now()->addHours(24)->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function notificationHandler(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'transaction_id' => 'required|string',
                'status' => 'required|string',
            ]);

            $invoice = DB::table('invoices')->where('payment_gateway_ref', $validated['transaction_id'])->first();
            if (!$invoice) {
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            if (in_array($validated['status'], ['settlement', 'capture'])) {
                DB::table('invoices')->where('id', $invoice->id)->update([
                    'status' => 'paid',
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $validated['transaction_id'],
                    'payment_status' => $validated['status'],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getStatus(string $transactionId): JsonResponse
    {
        try {
            $invoice = DB::table('invoices')->where('payment_gateway_ref', $transactionId)->first();
            if (!$invoice) {
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transaction_id' => $transactionId,
                    'invoice_id' => $invoice->id,
                    'amount' => (float) $invoice->amount,
                    'status' => $invoice->status,
                    'payment_method' => null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}