<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InspirationBoardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->query('session_id');
            if (!$sessionId) {
                return response()->json(['status' => 'error', 'message' => 'session_id is required'], 400);
            }

            $boards = DB::table('inspiration_boards')
                ->where('session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($board) {
                    $board->items = $board->items ? json_decode($board->items, true) : [];
                    return $board;
                });

            return response()->json(['status' => 'success', 'data' => $boards]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string',
                'title' => 'required|string|max:255',
            ]);

            $id = Str::uuid()->toString();
            DB::table('inspiration_boards')->insert([
                'id' => $id,
                'session_id' => $validated['session_id'],
                'title' => $validated['title'],
                'items' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $board = DB::table('inspiration_boards')->where('id', $id)->first();
            $board->items = [];
            return response()->json(['status' => 'success', 'data' => $board], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $board = DB::table('inspiration_boards')->where('id', $id)->first();
            if (!$board) {
                return response()->json(['status' => 'error', 'message' => 'Board not found'], 404);
            }

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'items' => 'nullable|array',
            ]);

            $updateData = ['updated_at' => now()];
            if (isset($validated['title'])) {
                $updateData['title'] = $validated['title'];
            }
            if (isset($validated['items'])) {
                $updateData['items'] = json_encode($validated['items']);
            }

            DB::table('inspiration_boards')->where('id', $id)->update($updateData);
            $board = DB::table('inspiration_boards')->where('id', $id)->first();
            $board->items = $board->items ? json_decode($board->items, true) : [];
            return response()->json(['status' => 'success', 'data' => $board]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function addItem(Request $request, string $id): JsonResponse
    {
        try {
            $board = DB::table('inspiration_boards')->where('id', $id)->first();
            if (!$board) {
                return response()->json(['status' => 'error', 'message' => 'Board not found'], 404);
            }

            $validated = $request->validate([
                'vendor_id' => 'nullable|string',
                'image_url' => 'nullable|string|max:500',
                'note' => 'nullable|string',
            ]);

            $items = $board->items ? json_decode($board->items, true) : [];
            $items[] = [
                'vendor_id' => $validated['vendor_id'] ?? null,
                'image_url' => $validated['image_url'] ?? null,
                'note' => $validated['note'] ?? null,
            ];

            DB::table('inspiration_boards')->where('id', $id)->update([
                'items' => json_encode($items),
                'updated_at' => now(),
            ]);

            $board = DB::table('inspiration_boards')->where('id', $id)->first();
            $board->items = json_decode($board->items, true);
            return response()->json(['status' => 'success', 'data' => $board]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $board = DB::table('inspiration_boards')->where('id', $id)->first();
            if (!$board) {
                return response()->json(['status' => 'error', 'message' => 'Board not found'], 404);
            }
            DB::table('inspiration_boards')->where('id', $id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Board deleted']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}