<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->query('session_id');
            if (!$sessionId) {
                return response()->json(['status' => 'error', 'message' => 'session_id is required'], 400);
            }

            $items = DB::table('wishlists')
                ->join('vendors', 'wishlists.vendor_id', '=', 'vendors.id')
                ->where('wishlists.session_id', $sessionId)
                ->select(
                    'wishlists.*',
                    'vendors.name as vendor_name',
                    'vendors.category',
                    'vendors.rating',
                    'vendors.starting_price',
                    'vendors.area'
                )
                ->orderBy('wishlists.created_at', 'desc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function toggle(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string',
                'vendor_id' => 'required|string',
            ]);

            $existing = DB::table('wishlists')
                ->where('session_id', $validated['session_id'])
                ->where('vendor_id', $validated['vendor_id'])
                ->first();

            if ($existing) {
                DB::table('wishlists')->where('id', $existing->id)->delete();
                return response()->json([
                    'status' => 'success',
                    'data' => ['action' => 'removed', 'vendor_id' => $validated['vendor_id']],
                ]);
            }

            DB::table('wishlists')->insert([
                'id' => Str::uuid()->toString(),
                'session_id' => $validated['session_id'],
                'vendor_id' => $validated['vendor_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => ['action' => 'added', 'vendor_id' => $validated['vendor_id']],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getBySession(string $sessionId): JsonResponse
    {
        try {
            $items = DB::table('wishlists')
                ->join('vendors', 'wishlists.vendor_id', '=', 'vendors.id')
                ->where('wishlists.session_id', $sessionId)
                ->select(
                    'wishlists.*',
                    'vendors.name as vendor_name',
                    'vendors.category',
                    'vendors.rating',
                    'vendors.starting_price',
                    'vendors.area'
                )
                ->orderBy('wishlists.created_at', 'desc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}