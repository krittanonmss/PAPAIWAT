<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleAddress extends Model
{
    protected $table = 'temple_addresses';

    protected $fillable = [
        'temple_id',
        'address_line',
        'province',
        'district',
        'subdistrict',
        'postal_code',
        'latitude',
        'longitude',
        'google_place_id',
        'google_maps_url',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }
}