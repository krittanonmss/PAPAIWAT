<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleNearbyRecommendation extends Model
{
    protected $table = 'temple_nearby_recommendations';

    protected $fillable = [
        'temple_id',
        'provider',
        'provider_place_id',
        'category',
        'name',
        'rating',
        'user_ratings_total',
        'latitude',
        'longitude',
        'distance_meters',
        'maps_url',
        'sort_score',
        'photo_names',
        'photo_path',
        'provider_types',
        'fetched_at',
        'expires_at',
        'stale_until',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'rating' => 'decimal:2',
        'user_ratings_total' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'distance_meters' => 'integer',
        'sort_score' => 'decimal:2',
        'photo_names' => 'array',
        'provider_types' => 'array',
        'fetched_at' => 'datetime',
        'expires_at' => 'datetime',
        'stale_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }

    public function scopeDisplayable(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereNull('stale_until')
                ->orWhere('stale_until', '>', now());
        });
    }

    public function getDistanceLabelAttribute(): ?string
    {
        if ($this->distance_meters === null) {
            return null;
        }

        return $this->distance_meters >= 1000
            ? number_format($this->distance_meters / 1000, 1).' กม.'
            : number_format($this->distance_meters).' ม.';
    }

    public function getPhotoUrlsAttribute(): array
    {
        if (! $this->photo_path) {
            return [];
        }

        return ['/storage/'.ltrim($this->photo_path, '/')];
    }
}
