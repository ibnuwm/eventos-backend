<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Scout\Searchable;

class Vendor extends Model
{
    use HasFactory, HasUuids, Searchable;

    protected $guarded = [];

    protected $casts = [
        'rating' => 'float',
        'sla_punctuality' => 'float',
        'starting_price' => 'float',
    ];

    /**
     * Konfigurasi atribut indeks Meilisearch
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'category' => $this->category,
            'pic_name' => $this->pic_name,
            'area' => $this->area,
            'rating' => (float) $this->rating,
            'sla_punctuality' => (float) $this->sla_punctuality,
            'starting_price' => (float) $this->starting_price,
        ];
    }
}
