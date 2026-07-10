<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorAnalyticsController extends Controller
{
    public function trackView(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'required|string',
                'ip_address' => 'nullable|string|max:45',
                'referrer' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
            ]);

            DB::table('vendor_page_views')->insert([
                'id' => Str::uuid()->toString(),
                'vendor_id' => $validated['vendor_id'],
                'ip_address' => $validated['ip_address'] ?? null,
                'referrer' => $validated['referrer'] ?? null,
                'city' => $validated['city'] ?? null,
                'viewed_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'View tracked'], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getVendorStats(string $vendorId): JsonResponse
    {
        try {
            $viewCount = DB::table('vendor_page_views')->where('vendor_id', $vendorId)->count();
            $uniqueVisitors = DB::table('vendor_page_views')
                ->where('vendor_id', $vendorId)
                ->whereNotNull('ip_address')
                ->distinct('ip_address')
                ->count('ip_address');
            $leadsCount = DB::table('vendor_storefront_leads')->where('vendor_id', $vendorId)->count();
            $avgRating = DB::table('vendor_reviews')
                ->where('vendor_id', $vendorId)
                ->avg('rating');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'vendor_id' => $vendorId,
                    'view_count' => $viewCount,
                    'unique_visitors' => $uniqueVisitors,
                    'leads_count' => $leadsCount,
                    'average_rating' => $avgRating ? round((float) $avgRating, 2) : 0,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function createLead(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'required|string',
                'client_name' => 'required|string|max:255',
                'client_whatsapp' => 'required|string|max:20',
                'message' => 'nullable|string',
            ]);

            $id = Str::uuid()->toString();
            DB::table('vendor_storefront_leads')->insert([
                'id' => $id,
                'vendor_id' => $validated['vendor_id'],
                'client_name' => $validated['client_name'],
                'client_whatsapp' => $validated['client_whatsapp'],
                'message' => $validated['message'] ?? null,
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $lead = DB::table('vendor_storefront_leads')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $lead], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getLeads(string $vendorId): JsonResponse
    {
        try {
            $leads = DB::table('vendor_storefront_leads')
                ->where('vendor_id', $vendorId)
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json(['status' => 'success', 'data' => $leads]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}