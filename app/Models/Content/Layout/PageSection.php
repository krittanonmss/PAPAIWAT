<?php

namespace App\Models\Content\Layout;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageSection extends Model
{
    use SoftDeletes;

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

    public function dataSource(): HasOne
    {
        return $this->hasOne(PageSectionDataSource::class);
    }

    public function scopeVisible($query)
    {
        return $query
            ->where('status', 'active')
            ->where('is_visible', true);
    }
}