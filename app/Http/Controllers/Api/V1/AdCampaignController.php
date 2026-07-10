<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdCampaignController extends Controller
{
    public function index(string $vendorId): JsonResponse
    {
        try {
            $campaigns = DB::table('vendor_ad_campaigns')
                ->where('vendor_id', $vendorId)
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json(['status' => 'success', 'data' => $campaigns]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'required|string',
                'campaign_name' => 'required|string|max:255',
                'daily_budget' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $id = Str::uuid()->toString();
            DB::table('vendor_ad_campaigns')->insert([
                'id' => $id,
                'vendor_id' => $validated['vendor_id'],
                'campaign_name' => $validated['campaign_name'],
                'daily_budget' => $validated['daily_budget'],
                'total_spent' => 0,
                'impressions' => 0,
                'clicks' => 0,
                'status' => 'active',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $campaign = DB::table('vendor_ad_campaigns')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $campaign], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function recordImpression(string $campaignId): JsonResponse
    {
        try {
            $campaign = DB::table('vendor_ad_campaigns')->where('id', $campaignId)->first();
            if (!$campaign) {
                return response()->json(['status' => 'error', 'message' => 'Campaign not found'], 404);
            }
            DB::table('vendor_ad_campaigns')->where('id', $campaignId)->increment('impressions');
            return response()->json(['status' => 'success', 'message' => 'Impression recorded']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function recordClick(string $campaignId): JsonResponse
    {
        try {
            $campaign = DB::table('vendor_ad_campaigns')->where('id', $campaignId)->first();
            if (!$campaign) {
                return response()->json(['status' => 'error', 'message' => 'Campaign not found'], 404);
            }
            $cpc = $campaign->daily_budget / 100;
            DB::table('vendor_ad_campaigns')->where('id', $campaignId)->update([
                'clicks' => $campaign->clicks + 1,
                'total_spent' => $campaign->total_spent + $cpc,
                'updated_at' => now(),
            ]);
            return response()->json(['status' => 'success', 'data' => ['total_spent' => $campaign->total_spent + $cpc]]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $campaign = DB::table('vendor_ad_campaigns')->where('id', $id)->first();
            if (!$campaign) {
                return response()->json(['status' => 'error', 'message' => 'Campaign not found'], 404);
            }

            $validated = $request->validate([
                'status' => 'required|string|in:active,paused,ended',
            ]);

            DB::table('vendor_ad_campaigns')->where('id', $id)->update([
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

            $campaign = DB::table('vendor_ad_campaigns')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $campaign]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}