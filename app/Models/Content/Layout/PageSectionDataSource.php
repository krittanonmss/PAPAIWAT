<?php

namespace App\Models\Content\Layout;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSectionDataSource extends Model
{
    protected $fillable = [
        'page_section_id',
        'source_type',
        'source_key',
        'filters',
        'sort',
        'limit',
    ];

    protected $casts = [
        'filters' => 'array',
        'sort' => 'array',
        'limit' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }
}