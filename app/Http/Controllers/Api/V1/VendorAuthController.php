<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class VendorAuthController extends Controller {
    public function login(Request $request): JsonResponse {
        $validated = $request->validate([
            'whatsapp' => 'required|string',
            'name' => 'required|string',
        ]);
        $vendor = Vendor::where('whatsapp', $validated['whatsapp'])->where('name', $validated['name'])->first();
        if (!$vendor) return response()->json(['status' => 'error', 'message' => 'Vendor tidak ditemukan'], 404);
        $token = Str::random(48);
        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor_id' => $vendor->id,
                'name' => $vendor->name,
                'category' => $vendor->category,
                'access_token' => $token,
            ],
        ]);
    }
    public function dashboard(Request $request): JsonResponse {
        $vendorId = $request->input('vendor_id');
        $vendor = Vendor::find($vendorId);
        if (!$vendor) return response()->json(['status' => 'error', 'message' => 'Vendor tidak ditemukan'], 404);
        $tasks = DB::table('project_tasks')->where('assigned_vendor_name', $vendor->name)->orderBy('created_at', 'desc')->get();
        $totalProjects = $tasks->count();
        $completedTasks = $tasks->where('is_completed', true)->count();
        $upcomingTasks = $tasks->where('is_completed', false);
        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'category' => $vendor->category,
                    'rating' => (float) $vendor->rating,
                    'sla_punctuality' => (float) $vendor->sla_punctuality,
                    'area' => $vendor->area,
                ],
                'stats' => [
                    'total_projects' => $totalProjects,
                    'completed_tasks' => $completedTasks,
                    'pending_tasks' => $upcomingTasks->count(),
                ],
                'upcoming_tasks' => $upcomingTasks->values(),
            ],
        ]);
    }
}
