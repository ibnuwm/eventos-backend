<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DamageClaim extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'deduction_amount_idr' => 'float',
    ];
}
