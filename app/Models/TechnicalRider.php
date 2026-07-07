<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TechnicalRider extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'requires_genset_backup' => 'boolean',
        'curfew_penalty_per_hour' => 'float',
    ];
}
