<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = DB::table('forum_topics');
            $category = $request->query('category');
            $search = $request->query('search');

            if ($category) {
                $query->where('category', $category);
            }
            if ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            }

            $topics = $query->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $topics]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $topic = DB::table('forum_topics')->where('id', $id)->first();
            if (!$topic) {
                return response()->json(['status' => 'error', 'message' => 'Topic not found'], 404);
            }

            DB::table('forum_topics')->where('id', $id)->increment('view_count');

            $replies = DB::table('forum_replies')
                ->where('topic_id', $id)
                ->orderBy('created_at', 'asc')
                ->get();

            $topic = DB::table('forum_topics')->where('id', $id)->first();
            $topic->replies = $replies;

            return response()->json(['status' => 'success', 'data' => $topic]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'author_name' => 'required|string|max:255',
                'author_whatsapp' => 'nullable|string|max:20',
                'category' => 'nullable|string|max:100',
            ]);

            $id = Str::uuid()->toString();
            DB::table('forum_topics')->insert([
                'id' => $id,
                'title' => $validated['title'],
                'content' => $validated['content'],
                'author_name' => $validated['author_name'],
                'author_whatsapp' => $validated['author_whatsapp'] ?? null,
                'category' => $validated['category'] ?? 'umum',
                'view_count' => 0,
                'reply_count' => 0,
                'is_pinned' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $topic = DB::table('forum_topics')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $topic], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function reply(Request $request, string $topicId): JsonResponse
    {
        try {
            $topic = DB::table('forum_topics')->where('id', $topicId)->first();
            if (!$topic) {
                return response()->json(['status' => 'error', 'message' => 'Topic not found'], 404);
            }

            $validated = $request->validate([
                'content' => 'required|string',
                'author_name' => 'required|string|max:255',
                'author_whatsapp' => 'nullable|string|max:20',
            ]);

            $id = Str::uuid()->toString();
            DB::table('forum_replies')->insert([
                'id' => $id,
                'topic_id' => $topicId,
                'content' => $validated['content'],
                'author_name' => $validated['author_name'],
                'author_whatsapp' => $validated['author_whatsapp'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('forum_topics')->where('id', $topicId)->increment('reply_count');

            $reply = DB::table('forum_replies')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $reply], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCategories(): JsonResponse
    {
        try {
            $categories = DB::table('forum_topics')
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');
            return response()->json(['status' => 'success', 'data' => $categories]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}