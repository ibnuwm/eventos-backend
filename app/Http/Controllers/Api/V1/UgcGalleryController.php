<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UgcGalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DB::table('ugc_galleries')->where('is_approved', true);
            $category = $request->query('category');
            $vendorId = $request->query('vendor_id');

            if ($category) {
                $query->where('category', $category);
            }
            if ($vendorId) {
                $query->whereJsonContains('tagged_vendor_ids', $vendorId);
            }

            $items = $query->orderBy('created_at', 'desc')->get()
                ->map(function ($item) {
                    $item->tagged_vendor_ids = $item->tagged_vendor_ids ? json_decode($item->tagged_vendor_ids, true) : [];
                    return $item;
                });

            return response()->json(['status' => 'success', 'data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string',
                'uploader_name' => 'required|string|max:255',
                'photo_url' => 'required|string|max:500',
                'caption' => 'nullable|string',
                'tagged_vendor_ids' => 'nullable|array',
                'tagged_vendor_ids.*' => 'string',
            ]);

            $id = Str::uuid()->toString();
            DB::table('ugc_galleries')->insert([
                'id' => $id,
                'session_id' => $validated['session_id'],
                'uploader_name' => $validated['uploader_name'],
                'photo_url' => $validated['photo_url'],
                'caption' => $validated['caption'] ?? null,
                'tagged_vendor_ids' => isset($validated['tagged_vendor_ids'])
                    ? json_encode($validated['tagged_vendor_ids'])
                    : null,
                'is_approved' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $item = DB::table('ugc_galleries')->where('id', $id)->first();
            $item->tagged_vendor_ids = $item->tagged_vendor_ids ? json_decode($item->tagged_vendor_ids, true) : [];
            return response()->json(['status' => 'success', 'data' => $item], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function approve(string $id): JsonResponse
    {
        try {
            $item = DB::table('ugc_galleries')->where('id', $id)->first();
            if (!$item) {
                return response()->json(['status' => 'error', 'message' => 'Gallery item not found'], 404);
            }
            DB::table('ugc_galleries')->where('id', $id)->update([
                'is_approved' => true,
                'updated_at' => now(),
            ]);
            return response()->json(['status' => 'success', 'message' => 'Gallery item approved']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getByVendor(string $vendorId): JsonResponse
    {
        try {
            $items = DB::table('ugc_galleries')
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function ($item) use ($vendorId) {
                    $tagged = $item->tagged_vendor_ids ? json_decode($item->tagged_vendor_ids, true) : [];
                    return in_array($vendorId, $tagged);
                })
                ->values()
                ->map(function ($item) {
                    $item->tagged_vendor_ids = $item->tagged_vendor_ids ? json_decode($item->tagged_vendor_ids, true) : [];
                    return $item;
                });

            return response()->json(['status' => 'success', 'data' => $items]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}