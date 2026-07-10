<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SponsoredContentController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $contents = DB::table('sponsored_contents')
                ->where('is_active', true)
                ->whereDate('end_date', '>=', now()->toDateString())
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json(['status' => 'success', 'data' => $contents]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'nullable|string',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'nullable|string|in:article,banner,video',
                'target_url' => 'nullable|string|max:500',
                'image_url' => 'nullable|string|max:500',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $id = Str::uuid()->toString();
            DB::table('sponsored_contents')->insert([
                'id' => $id,
                'vendor_id' => $validated['vendor_id'] ?? null,
                'title' => $validated['title'],
                'content' => $validated['content'],
                'type' => $validated['type'] ?? 'article',
                'target_url' => $validated['target_url'] ?? null,
                'image_url' => $validated['image_url'] ?? null,
                'is_active' => true,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $content = DB::table('sponsored_contents')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $content], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $content = DB::table('sponsored_contents')->where('id', $id)->first();
            if (!$content) {
                return response()->json(['status' => 'error', 'message' => 'Sponsored content not found'], 404);
            }
            return response()->json(['status' => 'success', 'data' => $content]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}