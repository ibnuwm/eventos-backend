<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionTier
{
    public function handle(Request $request, Closure $next, string $requiredTier): Response
    {
        $tenantId = $request->header('X-Tenant-ID', 'tenant-demo-uuid');

        $tenant = DB::table('tenants')->where('id', $tenantId)->first();

        if (!$tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant tidak ditemukan',
            ], 404);
        }

        $tierOrder = ['basic' => 0, 'pro' => 1, 'business' => 2, 'enterprise' => 3];
        $tenantLevel = $tierOrder[$tenant->subscription_tier] ?? -1;
        $requiredLevel = $tierOrder[$requiredTier] ?? 0;

        if ($tenantLevel < $requiredLevel) {
            return response()->json([
                'status' => 'error',
                'message' => "Fitur ini membutuhkan paket {$requiredTier}. Paket saat ini: {$tenant->subscription_tier}",
                'current_tier' => $tenant->subscription_tier,
                'required_tier' => $requiredTier,
            ], 403);
        }

        $request->merge(['_tenant_tier' => $tenant->subscription_tier]);

        return $next($request);
    }
}
