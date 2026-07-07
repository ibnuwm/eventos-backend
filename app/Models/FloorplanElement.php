<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FloorplanElement extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'pos_x' => 'float',
        'pos_y' => 'float',
    ];
}
