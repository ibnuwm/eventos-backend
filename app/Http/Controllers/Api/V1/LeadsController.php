<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID', 'tenant-demo-uuid');
        $leads = DB::table('leads')
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $leads,
        ]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate(['status' => 'required|string|in:new,contacted,quotation_sent,negotiation,won,lost']);

        $updated = DB::table('leads')
            ->where('id', $id)
            ->update(['status' => $request->input('status'), 'updated_at' => now()]);

        if (!$updated) {
            return response()->json(['status' => 'error', 'message' => 'Lead not found'], 404);
        }

        $lead = DB::table('leads')->where('id', $id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $lead,
            'message' => 'Lead status updated to ' . $request->input('status'),
        ]);
    }
}
