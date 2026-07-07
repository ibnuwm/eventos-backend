<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EscrowAccount extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'total_payment_received' => 'float',
        'vendor_hpp_escrow_holding' => 'float',
        'wo_margin_released' => 'float',
    ];

    public function disbursements()
    {
        return $this->hasMany(EscrowDisbursement::class);
    }
}
