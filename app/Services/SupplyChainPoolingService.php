<?php

namespace App\Services;

use App\Models\GroupBuyingPool;
use App\Models\GroupBuyingOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplyChainPoolingService
{
    /**
     * Inovasi Category King #6: B2B Supply Chain Pooling (Group Buying Network)
     * Menggabungkan pesanan bahan baku (bunga segar, panel LED) dari puluhan vendor
     * di akhir pekan yang sama untuk mendapatkan harga grosir grosir diskon 30%.
     */
    public function getActivePools(): array
    {
        return GroupBuyingPool::with('orders')->get()->toArray();
    }

    public function joinGroupBuying(string $tenantId, string $poolId, string $vendorName, int $orderQty): array
    {
        return DB::transaction(function () use ($tenantId, $poolId, $vendorName, $orderQty) {
            $pool = GroupBuyingPool::findOrFail($poolId);

            $savingsPerUnit = $pool->retail_price_per_unit - $pool->wholesale_price_per_unit;
            $totalSavings = $orderQty * $savingsPerUnit;

            $order = GroupBuyingOrder::create([
                'pool_id' => $pool->id,
                'tenant_id' => $tenantId,
                'vendor_id' => Str::uuid()->toString(),
                'vendor_name' => $vendorName,
                'order_qty' => $orderQty,
                'total_savings_idr' => $totalSavings
            ]);

            $pool->increment('current_pooled_qty', $orderQty);

            if ($pool->current_pooled_qty >= $pool->minimum_pool_qty) {
                $pool->update(['status' => 'threshold_reached']);
            }

            return [
                'success' => true,
                'pool_name' => $pool->item_name,
                'order_qty' => $orderQty,
                'wholesale_price' => $pool->wholesale_price_per_unit,
                'total_savings_idr' => $totalSavings,
                'pool_status' => $pool->status
            ];
        });
    }
}
