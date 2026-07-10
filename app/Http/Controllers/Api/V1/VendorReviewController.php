<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorReviewController extends Controller
{
    public function index(string $vendorId): JsonResponse
    {
        try {
            $reviews = DB::table('vendor_reviews')
                ->where('vendor_id', $vendorId)
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json(['status' => 'success', 'data' => $reviews]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'required|string',
                'reviewer_name' => 'required|string|max:255',
                'reviewer_whatsapp' => 'nullable|string|max:20',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string',
                'photo_url' => 'nullable|string|max:500',
            ]);

            $id = Str::uuid()->toString();
            DB::table('vendor_reviews')->insert([
                'id' => $id,
                'vendor_id' => $validated['vendor_id'],
                'reviewer_name' => $validated['reviewer_name'],
                'reviewer_whatsapp' => $validated['reviewer_whatsapp'] ?? null,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'photo_url' => $validated['photo_url'] ?? null,
                'is_verified' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $review = DB::table('vendor_reviews')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $review], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAverage(string $vendorId): JsonResponse
    {
        try {
            $avg = DB::table('vendor_reviews')
                ->where('vendor_id', $vendorId)
                ->avg('rating');

            $total = DB::table('vendor_reviews')
                ->where('vendor_id', $vendorId)
                ->count();

            $distribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $distribution[$i] = DB::table('vendor_reviews')
                    ->where('vendor_id', $vendorId)
                    ->where('rating', $i)
                    ->count();
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'average_rating' => $avg ? round((float) $avg, 2) : 0,
                    'total_reviews' => $total,
                    'distribution' => $distribution,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}