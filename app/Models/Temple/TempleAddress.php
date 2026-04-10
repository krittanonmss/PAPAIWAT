<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleAddress extends Model
{
    protected $fillable = [
        'temple_id',
        'address_line_1',
        'address_line_2',
        'subdistrict',
        'district',
        'province',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'google_maps_url',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }
}