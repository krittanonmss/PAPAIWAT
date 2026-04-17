<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleNearbyPlace extends Model
{
    protected $table = 'temple_nearby_places';

    protected $fillable = [
        'temple_id',
        'nearby_temple_id',
        'relation_type',
        'distance_km',
        'duration_minutes',
        'score',
        'sort_order',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'nearby_temple_id' => 'integer',
        'distance_km' => 'decimal:2',
        'duration_minutes' => 'integer',
        'score' => 'decimal:2',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }

    public function nearbyTemple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'nearby_temple_id');
    }
}