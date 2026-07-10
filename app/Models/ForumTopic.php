<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ForumTopic extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'view_count' => 'integer',
        'reply_count' => 'integer',
        'is_pinned' => 'boolean',
    ];
}
