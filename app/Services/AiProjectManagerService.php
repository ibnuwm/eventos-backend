<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AiProjectManagerService
{
    /**
     * Improvement #1: AI Project Manager
     * Otomatis membuat rancangan timeline T-minus, checklist sub-tugas divisi,
     * dan estimasi alokasi anggaran berdasarkan tipe acara dan tanggal pelaksanaan.
     */
    public function generateProjectPlan(string $tenantId, string $title, string $clientName, string $eventType, string $eventDate, int $paxCount): Project
    {
        return DB::transaction(function () use ($tenantId, $title, $clientName, $eventType, $eventDate, $paxCount) {
            // 1. Perhitungan Anggaran Otomatis (Budget Estimator AI)
            $baseCostPerPax = match (strtolower($eventType)) {
                'grand ballroom wedding' => 220000,
                'intimate garden wedding' => 180000,
                'corporate gala dinner' => 250000,
                default => 150000,
            };

            $contractValue = $paxCount * $baseCostPerPax;
            $vendorCost = round($contractValue * 0.65); // HPP ideal 65%
            $operationalCost = round($contractValue * 0.10); // Overhead 10%
            // Target Laba Bersih = 25%

            // 2. Buat Proyek Utama
            $project = Project::create([
                'tenant_id' => $tenantId,
                'title' => $title,
                'client_name' => $clientName,
                'event_date' => $eventDate,
                'contract_value' => $contractValue,
                'vendor_cost' => $vendorCost,
                'operational_cost' => $operationalCost,
                'payment_status' => 'dp_30',
                'days_remaining' => now()->diffInDays(floor($eventDate)),
                'progress_percentage' => 0
            ]);

            // 3. Template Milestone T-Minus AI berdasarkan tipe acara
            $templates = $this->getMilestoneTemplates($eventType, $eventDate);

            foreach ($templates as $tmpl) {
                ProjectTask::create([
                    'project_id' => $project->id,
                    'division' => $tmpl['division'],
                    'title' => $tmpl['title'],
                    'due_date' => $tmpl['due_date'],
                    'is_completed' => false,
                    'assigned_vendor_name' => $tmpl['default_vendor'] ?? 'Internal WO Team'
                ]);
            }

            return $project->load('tasks');
        });
    }

    protected function getMilestoneTemplates(string $eventType, string $eventDate): array
    {
        $d = \Carbon\Carbon::parse($eventDate);

        return [
            [
                'division' => 'Venue',
                'title' => 'T-180: Penandatanganan Kontrak Venue & Pembayaran Booking Fee',
                'due_date' => (clone $d)->subDays(180)->format('Y-m-d'),
                'default_vendor' => 'Grand Hotel Ballroom'
            ],
            [
                'division' => 'Catering',
                'title' => 'T-120: Pelaksanaan Food Testing Bersama Keluarga VIP (10 Pax)',
                'due_date' => (clone $d)->subDays(120)->format('Y-m-d'),
                'default_vendor' => 'Chef Gourmet Catering'
            ],
            [
                'division' => 'Decoration',
                'title' => 'T-60: Approval Sketsa 3D Layout Panggung & Lorong Pelaminan',
                'due_date' => (clone $d)->subDays(60)->format('Y-m-d'),
                'default_vendor' => 'Grand Rose Decor'
            ],
            [
                'division' => 'Sound & MC',
                'title' => 'T-30: Technical Meeting & Finalisasi Daftar Lagu Request',
                'due_date' => (clone $d)->subDays(30)->format('Y-m-d'),
                'default_vendor' => 'ProSound Entertainment'
            ],
            [
                'division' => 'Photography',
                'title' => 'T-7: Briefing Tim Kamera & Cek Memory Card Cadangan',
                'due_date' => (clone $d)->subDays(7)->format('Y-m-d'),
                'default_vendor' => 'Lumiere Photography'
            ],
            [
                'division' => 'Decoration',
                'title' => 'H-1: Loading Dock & Instalasi Panggung Utama jam 04.00 WIB',
                'due_date' => (clone $d)->subDays(1)->format('Y-m-d'),
                'default_vendor' => 'Grand Rose Decor'
            ],
        ];
    }
}
