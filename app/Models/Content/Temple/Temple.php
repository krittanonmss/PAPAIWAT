<?php

namespace App\Models\Content\Temple;

use App\Models\Content\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Temple extends Model
{
    protected $table = 'temples';

    protected $fillable = [
        'content_id',
        'temple_type',
        'sect',
        'architecture_style',
        'founded_year',
        'history',
        'dress_code',
        'recommended_visit_start_time',
        'recommended_visit_end_time',
    ];

    protected $casts = [
        'content_id' => 'integer',
        'recommended_visit_start_time' => 'string',
        'recommended_visit_end_time' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function address(): HasOne
    {
        return $this->hasOne(TempleAddress::class, 'temple_id');
    }

    public function openingHours(): HasMany
    {
        return $this->hasMany(TempleOpeningHour::class, 'temple_id')
            ->orderBy('day_of_week');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(TempleFee::class, 'temple_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function facilityItems(): HasMany
    {
        return $this->hasMany(TempleFacility::class, 'temple_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(TempleHighlight::class, 'temple_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function visitRules(): HasMany
    {
        return $this->hasMany(TempleVisitRule::class, 'temple_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function travelInfos(): HasMany
    {
        return $this->hasMany(TempleTravelInfo::class, 'temple_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function nearbyPlaces(): HasMany
    {
        return $this->hasMany(TempleNearbyPlace::class, 'temple_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function stat(): HasOne
    {
        return $this->hasOne(TempleStat::class, 'temple_id');
    }
}
