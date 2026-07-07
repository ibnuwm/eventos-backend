<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'event_date' => 'date',
        'contract_value' => 'float',
        'vendor_cost' => 'float',
        'operational_cost' => 'float',
    ];

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }
}
