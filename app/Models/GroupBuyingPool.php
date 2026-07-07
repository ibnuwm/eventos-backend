<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class GroupBuyingPool extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'target_weekend_date' => 'date',
        'retail_price_per_unit' => 'float',
        'wholesale_price_per_unit' => 'float',
    ];

    public function orders()
    {
        return $this->hasMany(GroupBuyingOrder::class, 'pool_id');
    }
}
