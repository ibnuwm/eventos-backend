<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $projectId = $request->input('project_id', 'proj-1');
        $messages = DB::table('chat_messages')
            ->where('project_id', $projectId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|string',
            'channel' => 'required|string',
            'sender_name' => 'required|string',
            'sender_role' => 'required|string',
            'text' => 'required|string',
        ]);

        $id = (string) Str::uuid();
        $timestamp = now()->format('H.i') . ' WIB';

        DB::table('chat_messages')->insert([
            'id' => $id,
            'project_id' => $validated['project_id'],
            'channel' => $validated['channel'],
            'sender_name' => $validated['sender_name'],
            'sender_role' => $validated['sender_role'],
            'text' => $validated['text'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $message = DB::table('chat_messages')->where('id', $id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $message,
            'message' => 'Message sent',
        ], 201);
    }
}
