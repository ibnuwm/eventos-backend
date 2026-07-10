<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $posts = DB::table('blog_posts')
                ->where('is_published', true)
                ->select('id', 'title', 'slug', 'excerpt', 'featured_image', 'author', 'category', 'tags', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json(['status' => 'success', 'data' => $posts]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $post = DB::table('blog_posts')->where('slug', $slug)->first();
            if (!$post) {
                return response()->json(['status' => 'error', 'message' => 'Blog post not found'], 404);
            }
            return response()->json(['status' => 'success', 'data' => $post]);
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
                'excerpt' => 'nullable|string',
                'category' => 'nullable|string|max:100',
                'tags' => 'nullable|array',
                'featured_image' => 'nullable|string|max:500',
                'author' => 'nullable|string|max:255',
            ]);

            $id = Str::uuid()->toString();
            $slug = Str::slug($validated['title']) . '-' . Str::random(5);

            DB::table('blog_posts')->insert([
                'id' => $id,
                'title' => $validated['title'],
                'slug' => $slug,
                'content' => $validated['content'],
                'excerpt' => $validated['excerpt'] ?? null,
                'category' => $validated['category'] ?? 'tips',
                'tags' => isset($validated['tags']) ? json_encode($validated['tags']) : null,
                'featured_image' => $validated['featured_image'] ?? null,
                'author' => $validated['author'] ?? 'EventOS',
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $post = DB::table('blog_posts')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $post], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $post = DB::table('blog_posts')->where('id', $id)->first();
            if (!$post) {
                return response()->json(['status' => 'error', 'message' => 'Blog post not found'], 404);
            }

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string',
                'excerpt' => 'nullable|string',
                'category' => 'nullable|string|max:100',
                'tags' => 'nullable|array',
                'featured_image' => 'nullable|string|max:500',
                'author' => 'nullable|string|max:255',
            ]);

            $updateData = [];
            foreach (['title', 'content', 'excerpt', 'category', 'featured_image', 'author'] as $field) {
                if (isset($validated[$field])) {
                    $updateData[$field] = $validated[$field];
                }
            }
            if (isset($validated['tags'])) {
                $updateData['tags'] = json_encode($validated['tags']);
            }
            $updateData['updated_at'] = now();

            DB::table('blog_posts')->where('id', $id)->update($updateData);
            $post = DB::table('blog_posts')->where('id', $id)->first();
            return response()->json(['status' => 'success', 'data' => $post]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $post = DB::table('blog_posts')->where('id', $id)->first();
            if (!$post) {
                return response()->json(['status' => 'error', 'message' => 'Blog post not found'], 404);
            }
            DB::table('blog_posts')->where('id', $id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Blog post deleted']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}