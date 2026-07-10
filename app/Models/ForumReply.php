<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ForumReply extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }
}
