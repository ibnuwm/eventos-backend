<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID', 'tenant-demo-uuid');
        $staff = DB::table('staff_crews')
            ->where('tenant_id', $tenantId)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $staff,
        ]);
    }
}
