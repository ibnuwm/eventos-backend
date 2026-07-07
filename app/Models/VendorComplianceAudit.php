<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VendorComplianceAudit extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'legal_npwp_verified' => 'boolean',
    ];
}
