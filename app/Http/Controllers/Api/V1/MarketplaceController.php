<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Cari vendor menggunakan Meilisearch (<50ms sub-second search) diutamakan berdasarkan SLA Punctuality
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $category = $request->input('category', 'All');

        if (!empty($query)) {
            // Pencarian Meilisearch Scout
            $vendors = Vendor::search($query)
                ->when($category !== 'All', function ($scout) use ($category) {
                    $scout->where('category', $category);
                })
                ->orderBy('sla_punctuality', 'desc')
                ->get();
        } else {
            // Standard Eloquent MySQL fallback
            $vendors = Vendor::when($category !== 'All', function ($q) use ($category) {
                    $q->where('category', $category);
                })
                ->orderBy('sla_punctuality', 'desc')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'meta' => [
                'engine' => 'Meilisearch Scout (SLA Punctuality Ranked)',
                'total' => $vendors->count()
            ],
            'data' => $vendors
        ]);
    }
}
