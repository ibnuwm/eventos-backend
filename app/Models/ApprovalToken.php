<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ApprovalToken extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'approved_documents' => 'array',
        'expires_at' => 'datetime',
    ];
}
