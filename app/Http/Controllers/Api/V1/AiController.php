<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AiCopilotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    protected AiCopilotService $aiService;

    public function __construct(AiCopilotService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Endpoint untuk memproses prompt bahasa alami (Modul 12 AI Copilot)
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:1000',
            'context' => 'nullable|array'
        ]);

        $response = $this->aiService->generateResponse($validated['prompt'], $validated['context'] ?? []);

        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }
}
