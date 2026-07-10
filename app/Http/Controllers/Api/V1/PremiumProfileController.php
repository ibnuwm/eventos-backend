<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PremiumProfileController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $profiles = DB::table('vendor_premium_profiles')
                ->join('vendors', 'vendor_premium_profiles.vendor_id', '=', 'vendors.id')
                ->select(
                    'vendor_premium_profiles.*',
                    'vendors.name as vendor_name',
                    'vendors.category',
                    'vendors.rating',
                    'vendors.starting_price',
                    'vendors.area'
                )
                ->orderBy('vendor_premium_profiles.priority_score', 'desc')
                ->get();
            return response()->json(['status' => 'success', 'data' => $profiles]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'required|string',
                'badge_type' => 'nullable|string|in:premium,enterprise,verified',
                'is_featured' => 'nullable|boolean',
                'priority_score' => 'nullable|integer|min:0',
                'subscription_start' => 'required|date',
                'subscription_end' => 'required|date|after:subscription_start',
            ]);

            $existing = DB::table('vendor_premium_profiles')
                ->where('vendor_id', $validated['vendor_id'])
                ->first();

            if ($existing) {
                DB::table('vendor_premium_profiles')
                    ->where('vendor_id', $validated['vendor_id'])
                    ->update([
                        'badge_type' => $validated['badge_type'] ?? $existing->badge_type,
                        'is_featured' => $validated['is_featured'] ?? $existing->is_featured,
                        'priority_score' => $validated['priority_score'] ?? $existing->priority_score,
                        'subscription_start' => $validated['subscription_start'],
                        'subscription_end' => $validated['subscription_end'],
                        'updated_at' => now(),
                    ]);
                $profile = DB::table('vendor_premium_profiles')
                    ->where('vendor_id', $validated['vendor_id'])
                    ->first();
                return response()->json(['status' => 'success', 'data' => $profile]);
            }

            $id = Str::uuid()->toString();
            DB::table('vendor_premium_profiles')->insert([
                'id' => $id,
                'vendor_id' => $validated['vendor_id'],
                'badge_type' => $validated['badge_type'] ?? 'premium',
                'is_featured' => $validated['is_featured'] ?? false,
                'priority_score' => $validated['priority_score'] ?? 0,
                'subscription_start' => $validated['subscription_start'],
                'subscription_end' => $validated['subscription_end'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $profile = DB::table('vendor_premium_profiles')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $profile], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getFeatured(): JsonResponse
    {
        try {
            $vendors = DB::table('vendor_premium_profiles')
                ->join('vendors', 'vendor_premium_profiles.vendor_id', '=', 'vendors.id')
                ->where('vendor_premium_profiles.is_featured', true)
                ->whereDate('vendor_premium_profiles.subscription_end', '>=', now()->toDateString())
                ->select(
                    'vendor_premium_profiles.*',
                    'vendors.name as vendor_name',
                    'vendors.category',
                    'vendors.rating',
                    'vendors.starting_price',
                    'vendors.area',
                    'vendors.pic_name',
                    'vendors.whatsapp'
                )
                ->orderBy('vendor_premium_profiles.priority_score', 'desc')
                ->get();
            return response()->json(['status' => 'success', 'data' => $vendors]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function verifyBadge(string $vendorId): JsonResponse
    {
        try {
            $profile = DB::table('vendor_premium_profiles')
                ->where('vendor_id', $vendorId)
                ->first();

            if (!$profile) {
                return response()->json(['status' => 'error', 'message' => 'No premium profile found for this vendor'], 404);
            }

            $isActive = now()->between($profile->subscription_start, $profile->subscription_end);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'vendor_id' => $vendorId,
                    'badge_type' => $profile->badge_type,
                    'is_featured' => (bool) $profile->is_featured,
                    'is_active' => $isActive,
                    'subscription_start' => $profile->subscription_start,
                    'subscription_end' => $profile->subscription_end,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}