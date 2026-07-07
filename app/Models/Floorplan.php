<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Floorplan extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function elements()
    {
        return $this->hasMany(FloorplanElement::class);
    }
}
