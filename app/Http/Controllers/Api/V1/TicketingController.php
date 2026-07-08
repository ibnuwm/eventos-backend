<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class TicketingController extends Controller {
    public function getEvent(string $eventId): JsonResponse {
        $event = DB::table('event_tickets')->where('id', $eventId)->first();
        if (!$event) return response()->json(['status' => 'error', 'message' => 'Event tidak ditemukan'], 404);
        $tiers = DB::table('ticket_tiers')->where('event_ticket_id', $eventId)->get();
        return response()->json([
            'status' => 'success',
            'data' => ['event' => $event, 'tiers' => $tiers],
        ]);
    }
    public function listEvents(): JsonResponse {
        $events = DB::table('event_tickets')->orderBy('event_date', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $events]);
    }
    public function createOrder(Request $request): JsonResponse {
        $validated = $request->validate([
            'event_ticket_id' => 'required|string',
            'tier_id' => 'required|string',
            'buyer_name' => 'required|string',
            'buyer_email' => 'nullable|email',
            'buyer_whatsapp' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);
        $tier = DB::table('ticket_tiers')->where('id', $validated['tier_id'])->first();
        if (!$tier) return response()->json(['status' => 'error', 'message' => 'Tier tiket tidak ditemukan'], 404);
        $available = $tier->quota - $tier->sold;
        if ($validated['quantity'] > $available) {
            return response()->json(['status' => 'error', 'message' => "Sisa tiket tersedia: {$available}"], 409);
        }
        $total = $tier->price * $validated['quantity'];
        $id = Str::uuid()->toString();
        $qrToken = Str::random(32);
        DB::table('ticket_orders')->insert([
            'id' => $id,
            'tier_id' => $validated['tier_id'],
            'buyer_name' => $validated['buyer_name'],
            'buyer_email' => $validated['buyer_email'] ?? null,
            'buyer_whatsapp' => $validated['buyer_whatsapp'],
            'quantity' => $validated['quantity'],
            'total' => $total,
            'status' => 'pending',
            'qr_token' => $qrToken,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $id,
                'total' => $total,
                'qr_token' => $qrToken,
                'ticket_url' => rtrim(config('app.frontend_url', 'http://localhost:3000'), '/') . '/tickets/' . $qrToken,
                'status' => 'pending',
            ],
        ]);
    }
    public function verifyTicket(string $qrToken): JsonResponse {
        $order = DB::table('ticket_orders')->where('qr_token', $qrToken)->first();
        if (!$order) return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan'], 404);
        $tier = DB::table('ticket_tiers')->where('id', $order->tier_id)->first();
        $event = null;
        if ($tier) $event = DB::table('event_tickets')->where('id', $tier->event_ticket_id)->first();
        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $order->id,
                'buyer_name' => $order->buyer_name,
                'quantity' => $order->quantity,
                'total' => (float) $order->total,
                'status' => $order->status,
                'tier_name' => $tier->tier_name ?? '-',
                'event_title' => $event->event_title ?? '-',
                'event_date' => $event->event_date ?? '-',
                'venue' => $event->venue ?? '-',
            ],
        ]);
    }
    public function checkIn(string $qrToken): JsonResponse {
        $order = DB::table('ticket_orders')->where('qr_token', $qrToken)->first();
        if (!$order) return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan'], 404);
        if ($order->status === 'used') return response()->json(['status' => 'error', 'message' => 'Tiket sudah dipakai'], 409);
        if ($order->status !== 'paid') return response()->json(['status' => 'error', 'message' => 'Tiket belum dibayar'], 402);
        DB::table('ticket_orders')->where('id', $order->id)->update(['status' => 'used', 'updated_at' => now()]);
        DB::table('ticket_tiers')->where('id', $order->tier_id)->increment('sold');
        return response()->json(['status' => 'success', 'message' => 'Check-in berhasil!']);
    }
}
