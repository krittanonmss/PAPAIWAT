<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Temple extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'subtitle',
        'excerpt',
        'description',
        'history',
        'status',
        'is_featured',
        'view_count',
        'sort_order',
        'cover_media_id',
        'meta_title',
        'meta_description',
        'published_at',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    public function address(): HasOne
    {
        return $this->hasOne(TempleAddress::class);
    }

    public function openingHours(): HasMany
    {
        return $this->hasMany(TempleOpeningHour::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(TempleFee::class);
    }

    public function templeFacilities(): HasMany
    {
        return $this->hasMany(TempleFacility::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'temple_facilities')
            ->withPivot('note')
            ->withTimestamps();
    }

    public function templeMedia(): HasMany
    {
        return $this->hasMany(TempleMedia::class);
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'temple_media')
            ->withPivot(['collection', 'alt_text', 'caption', 'is_cover', 'sort_order'])
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(TempleCategory::class, 'temple_category_items')
            ->withTimestamps();
    }

    public function categoryItems(): HasMany
    {
        return $this->hasMany(TempleCategoryItem::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(TempleHighlight::class)->orderBy('sort_order');
    }

    public function visitRules(): HasMany
    {
        return $this->hasMany(TempleVisitRule::class)->orderBy('sort_order');
    }

    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function updatedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }
}