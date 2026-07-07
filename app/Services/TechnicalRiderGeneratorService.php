<?php

namespace App\Services;

use App\Models\TechnicalRider;
use Illuminate\Support\Str;

class TechnicalRiderGeneratorService
{
    /**
     * Inovasi Category King #5: Automated Technical Rider PDF Generator
     * Menghitung kebutuhan spesifikasi listrik (kVA), backup genset, dan aturan jam malam
     * secara otomatis berdasarkan kombinasi vendor sound, lampu, dan panggung.
     */
    public function compileTechnicalRider(string $tenantId, string $projectId, array $selectedVendors): TechnicalRider
    {
        $minPower = 20; // Base 20 kVA
        foreach ($selectedVendors as $v) {
            if (str_contains(strtolower($v), 'sound') || str_contains(strtolower($v), 'audio')) {
                $minPower += 15; // +15 kVA for heavy audio
            }
            if (str_contains(strtolower($v), 'led') || str_contains(strtolower($v), 'screen')) {
                $minPower += 10; // +10 kVA for LED screens
            }
        }

        return TechnicalRider::create([
            'tenant_id' => $tenantId,
            'project_id' => $projectId,
            'document_number' => 'RIDER-' . strtoupper(Str::random(8)),
            'minimum_power_kva' => $minPower,
            'requires_genset_backup' => true,
            'lighting_color_temp' => '4000K Natural Warm White (Wajib untuk Liputan Kamera)',
            'loading_curfew' => '05.30 WIB Pagi',
            'curfew_penalty_per_hour' => 5000000,
            'status' => 'compiled'
        ]);
    }
}
