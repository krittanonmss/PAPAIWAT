<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleTravelInfo extends Model
{
    protected $table = 'temple_travel_infos';

    protected $fillable = [
        'temple_id',
        'travel_type',
        'start_place',
        'distance_km',
        'duration_minutes',
        'cost_estimate',
        'note',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'distance_km' => 'decimal:2',
        'duration_minutes' => 'integer',
        'cost_estimate' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }
}