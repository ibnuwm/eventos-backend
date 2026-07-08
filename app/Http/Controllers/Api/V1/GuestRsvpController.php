<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class GuestRsvpController extends Controller {
    public function index(Request $request): JsonResponse {
        $tenantId = $request->header('X-Tenant-ID', 'tenant-demo-uuid');
        $projectId = $request->input('project_id', 'proj-1');
        $guests = DB::table('guests')->where('tenant_id', $tenantId)->where('project_id', $projectId)->orderBy('created_at')->get();
        return response()->json(['status' => 'success', 'data' => $guests]);
    }
    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'project_id' => 'required|string',
            'name' => 'required|string',
            'whatsapp' => 'nullable|string',
            'category' => 'nullable|string',
            'guest_count' => 'nullable|integer|min:1',
            'menu_choice' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $id = Str::uuid()->toString();
        $token = Str::random(32);
        DB::table('guests')->insert([
            'id' => $id,
            'tenant_id' => $request->header('X-Tenant-ID', 'tenant-demo-uuid'),
            'project_id' => $validated['project_id'],
            'name' => $validated['name'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'category' => $validated['category'] ?? 'Umum',
            'guest_count' => $validated['guest_count'] ?? 1,
            'menu_choice' => $validated['menu_choice'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'token' => $token,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json([
            'status' => 'success',
            'data' => ['id' => $id, 'name' => $validated['name'], 'token' => $token, 'rsvp_url' => rtrim(config('app.frontend_url', 'http://localhost:3000'), '/') . '/rsvp/' . $token],
        ]);
    }
    public function rsvpVerify(string $token): JsonResponse {
        $guest = DB::table('guests')->where('token', $token)->first();
        if (!$guest) return response()->json(['status' => 'error', 'message' => 'Undangan tidak ditemukan'], 404);
        $project = DB::table('projects')->where('id', $guest->project_id)->first();
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $guest->id,
                'name' => $guest->name,
                'guest_count' => $guest->guest_count,
                'rsvp_status' => $guest->rsvp_status,
                'menu_choice' => $guest->menu_choice,
                'table_number' => $guest->table_number,
                'event_title' => $project->title ?? '-',
                'event_date' => $project->event_date ?? '-',
                'venue_name' => $project->venue_name ?? '-',
                'category' => $guest->category,
            ],
        ]);
    }
    public function rsvpConfirm(Request $request, string $token): JsonResponse {
        $guest = DB::table('guests')->where('token', $token)->first();
        if (!$guest) return response()->json(['status' => 'error', 'message' => 'Undangan tidak ditemukan'], 404);
        $validated = $request->validate([
            'rsvp_status' => 'required|string|in:confirmed,declined',
            'guest_count' => 'nullable|integer|min:1',
            'menu_choice' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        DB::table('guests')->where('id', $guest->id)->update([
            'rsvp_status' => $validated['rsvp_status'],
            'guest_count' => $validated['guest_count'] ?? $guest->guest_count,
            'menu_choice' => $validated['menu_choice'] ?? $guest->menu_choice,
            'notes' => $validated['notes'] ?? $guest->notes,
            'updated_at' => now(),
        ]);
        return response()->json([
            'status' => 'success',
            'message' => $validated['rsvp_status'] === 'confirmed' ? '✔ Konfirmasi kehadiran berhasil!' : '✖ Maaf, Anda tidak dapat hadir.',
        ]);
    }
}
