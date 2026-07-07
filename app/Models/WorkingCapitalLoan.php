<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WorkingCapitalLoan extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'contract_hpp_value' => 'float',
        'loan_amount_requested' => 'float',
        'platform_fee_percentage' => 'float',
        'net_disbursement_idr' => 'float',
    ];
}
