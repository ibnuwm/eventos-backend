<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RundownController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $projectId = $request->input('project_id', 'proj-1');
        $items = DB::table('rundown_items')
            ->where('project_id', $projectId)
            ->orderBy('sequence_order', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|string',
            'time_slot' => 'required|string',
            'duration_minutes' => 'required|integer',
            'activity_title' => 'required|string',
            'division_pic' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $maxSeq = DB::table('rundown_items')
            ->where('project_id', $validated['project_id'])
            ->max('sequence_order') ?? 0;

        $id = (string) Str::uuid();
        DB::table('rundown_items')->insert([
            'id' => $id,
            'project_id' => $validated['project_id'],
            'time_slot' => $validated['time_slot'],
            'duration_minutes' => $validated['duration_minutes'],
            'activity_title' => $validated['activity_title'],
            'division_pic' => $validated['division_pic'],
            'notes' => $validated['notes'] ?? null,
            'sequence_order' => $maxSeq + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $item = DB::table('rundown_items')->where('id', $id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $item,
            'message' => 'Rundown item added',
        ], 201);
    }
}
