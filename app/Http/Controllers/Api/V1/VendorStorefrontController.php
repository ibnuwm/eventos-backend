<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorStorefrontController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $category = $request->input('category', 'All');

        $vendors = Vendor::when(!empty($query), fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->when($category !== 'All', fn($q) => $q->where('category', $category))
            ->orderBy('sla_punctuality', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $vendors->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'category' => $v->category,
                'pic_name' => $v->pic_name,
                'whatsapp' => $v->whatsapp,
                'rating' => (float) $v->rating,
                'sla_punctuality' => (float) $v->sla_punctuality,
                'starting_price' => (float) $v->starting_price,
                'area' => $v->area,
            ]),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['status' => 'error', 'message' => 'Vendor tidak ditemukan'], 404);
        }

        $taskCount = \Illuminate\Support\Facades\DB::table('project_tasks')
            ->where('assigned_vendor_name', $vendor->name)
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'category' => $vendor->category,
                'pic_name' => $vendor->pic_name,
                'whatsapp' => $vendor->whatsapp,
                'rating' => (float) $vendor->rating,
                'sla_punctuality' => (float) $vendor->sla_punctuality,
                'starting_price' => (float) $vendor->starting_price,
                'area' => $vendor->area,
                'npwp' => $vendor->npwp,
                'bank_account_info' => $vendor->bank_account_info,
                'total_projects_handled' => $taskCount,
            ],
        ]);
    }
}
