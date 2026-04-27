<?php

namespace App\Models\Content\Layout;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'name',
        'section_key',
        'component_key',
        'settings',
        'content',
        'status',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'content' => 'array',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'active')
            ->where('is_visible', true);
    }
}