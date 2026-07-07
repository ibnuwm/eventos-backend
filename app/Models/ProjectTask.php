<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectTask extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
    ];
}
