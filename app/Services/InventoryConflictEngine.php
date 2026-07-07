<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InventoryConflictEngine
{
    /**
     * Memeriksa dan mendeteksi bentrok inventaris pada tanggal tertentu
     */
    public function detectConflicts(string $tenantId, string $date): array
    {
        // Dalam implementasi nyata, query menghitung sum(qty) pesanan per item_id di tanggal $date
        // Simulasikan deteksi bentrok dari tabel inventory_items
        $items = DB::table('inventory_items')
            ->where('tenant_id', $tenantId)
            ->where('booked_for_date', $date)
            ->get();

        $conflicts = [];
        foreach ($items as $item) {
            if ($item->allocated_qty > $item->total_stock) {
                $deficit = $item->allocated_qty - $item->total_stock;
                $conflicts[] = [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'total_stock' => $item->total_stock,
                    'allocated_qty' => $item->allocated_qty,
                    'deficit' => $deficit,
                    'message' => "🚨 PREDICTIVE CONFLICT: {$item->name} kekurangan {$deficit} unit untuk tanggal {$date}!"
                ];

                DB::table('inventory_items')->where('id', $item->id)->update(['has_conflict' => true]);
            }
        }

        return [
            'has_conflicts' => count($conflicts) > 0,
            'conflict_count' => count($conflicts),
            'details' => $conflicts
        ];
    }
}
