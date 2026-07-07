<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID', 'tenant-demo-uuid');
        $projects = DB::table('projects')
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();

        $result = [];
        foreach ($projects as $project) {
            $tasks = DB::table('project_tasks')
                ->where('project_id', $project->id)
                ->orderBy('created_at', 'asc')
                ->get();
            $project->tasks = $tasks;
            $result[] = $project;
        }

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function toggleTask(Request $request, string $taskId): JsonResponse
    {
        $task = DB::table('project_tasks')->where('id', $taskId)->first();
        if (!$task) {
            return response()->json(['status' => 'error', 'message' => 'Task not found'], 404);
        }

        $newCompleted = !$task->is_completed;
        DB::table('project_tasks')
            ->where('id', $taskId)
            ->update(['is_completed' => $newCompleted, 'updated_at' => now()]);

        $projectId = $task->project_id;
        $allTasks = DB::table('project_tasks')->where('project_id', $projectId)->get();
        $completedCount = $allTasks->filter(fn($t) => $t->is_completed)->count();
        $progressPercentage = $allTasks->count() > 0
            ? round(($completedCount / $allTasks->count()) * 100)
            : 0;

        DB::table('projects')
            ->where('id', $projectId)
            ->update(['progress_percentage' => $progressPercentage, 'updated_at' => now()]);

        $updatedTask = DB::table('project_tasks')->where('id', $taskId)->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'task' => $updatedTask,
                'project_id' => $projectId,
                'progress_percentage' => $progressPercentage,
            ],
            'message' => 'Task status updated',
        ]);
    }
}
