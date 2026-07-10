<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VirtualExpoBooth extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
        'visitor_count' => 'integer',
        'lead_count' => 'integer',
    ];
}
