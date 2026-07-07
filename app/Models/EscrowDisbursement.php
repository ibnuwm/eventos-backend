<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EscrowDisbursement extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'disbursement_amount' => 'float',
        'disbursed_at' => 'datetime',
    ];
}
