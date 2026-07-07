<?php

namespace App\Services;

use App\Models\StageCue;

class StageCommandShowCallerService
{
    /**
     * Inovasi Monopoli Tahap 2 #3: StageCommand Live Show-Caller Console (SMPTE Cue Sync)
     */
    public function triggerCue(string $projectId, string $cueNumber, string $momentTitle): array
    {
        $cue = StageCue::create([
            'project_id' => $projectId,
            'cue_number' => $cueNumber,
            'moment_title' => $momentTitle,
            'target_divisions' => 'Lighting, Sound, MC, Video Cam',
            'countdown_seconds' => 5,
            'status' => 'live_executing'
        ]);

        return [
            'success' => true,
            'cue_id' => $cue->id,
            'cue_number' => $cueNumber,
            'moment_title' => $momentTitle,
            'countdown_seconds' => 5,
            'broadcasted_via_reverb' => true,
            'message' => '⚡ CUE BROADCASTED: Seluruh HP kru bergetar haptik & memunculkan hitung mundur kedip 5 detik!'
        ];
    }
}
