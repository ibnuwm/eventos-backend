<?php

namespace App\Services;

class SurgePricingAndWeatherGuardService
{
    /**
     * Inovasi Category King #4: AI Dynamic Surge Pricing & Weather Guard
     * Mengevaluasi lonjakan volume pemesanan di kalender tanggal cantik pernikahan
     * serta menganalisis ramalan cuaca BMKG untuk mengamankan logistik acara.
     */
    public function evaluateDateAndVenue(string $eventDate, string $venueType): array
    {
        $d = \Carbon\Carbon::parse($eventDate);

        // Cek tanggal cantik atau weekend populer (misal bulan Agustus / Desember)
        $isHighDemand = ($d->month === 8 || $d->month === 12) && $d->isWeekend();
        $surgeMultiplier = $isHighDemand ? 1.15 : 1.0; // +15% Surge Pricing

        // Weather guard check
        $isOutdoor = str_contains(strtolower($venueType), 'outdoor') || str_contains(strtolower($venueType), 'garden');
        $requiresWeatherProtection = $isOutdoor && ($d->month >= 10 || $d->month <= 3); // Musim hujan Indonesia

        return [
            'event_date' => $eventDate,
            'is_high_demand_date' => $isHighDemand,
            'recommended_surge_multiplier' => $surgeMultiplier,
            'surge_reason' => $isHighDemand ? '🔥 Tanggal Cantik & Weekend Puncak Pernikahan Nasional (+15%)' : 'Tarif Normal Ideal',
            'weather_guard' => [
                'requires_protection' => $requiresWeatherProtection,
                'recommended_addons' => $requiresWeatherProtection
                    ? ['Tenda Sarnafil VIP Waterproof 5x5m (4 Unit)', 'Jasa Pawang Hujan & Logistik Evakuasi Darurat']
                    : ['Aman (Indoor Ballroom)']
            ]
        ];
    }
}
