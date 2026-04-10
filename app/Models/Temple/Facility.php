<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    protected $fillable = [
        'name',
        'key',
        'icon',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function templeFacilities(): HasMany
    {
        return $this->hasMany(TempleFacility::class);
    }

    public function temples(): BelongsToMany
    {
        return $this->belongsToMany(Temple::class, 'temple_facilities')
            ->withPivot('note')
            ->withTimestamps();
    }
}