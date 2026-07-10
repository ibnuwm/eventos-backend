<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $tenantId = $request->header('X-Tenant-ID');
            if (!$tenantId) {
                return response()->json(['status' => 'error', 'message' => 'X-Tenant-ID header is required'], 400);
            }

            $keys = DB::table('api_keys')
                ->where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'name', 'permissions', 'is_active', 'last_used_at', 'created_at']);
            return response()->json(['status' => 'success', 'data' => $keys]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $tenantId = $request->header('X-Tenant-ID');
            if (!$tenantId) {
                return response()->json(['status' => 'error', 'message' => 'X-Tenant-ID header is required'], 400);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'permissions' => 'nullable|array',
            ]);

            $id = Str::uuid()->toString();
            $key = Str::random(64);

            DB::table('api_keys')->insert([
                'id' => $id,
                'tenant_id' => $tenantId,
                'name' => $validated['name'],
                'key' => $key,
                'permissions' => isset($validated['permissions']) ? json_encode($validated['permissions']) : null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $id,
                    'name' => $validated['name'],
                    'key' => $key,
                    'permissions' => $validated['permissions'] ?? [],
                    'is_active' => true,
                    'created_at' => now(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function revoke(string $id): JsonResponse
    {
        try {
            $apiKey = DB::table('api_keys')->where('id', $id)->first();
            if (!$apiKey) {
                return response()->json(['status' => 'error', 'message' => 'API key not found'], 404);
            }

            DB::table('api_keys')->where('id', $id)->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'API key revoked']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}