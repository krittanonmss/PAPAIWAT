<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaUsage extends Model
{
    protected $table = 'media_usages';

    protected $fillable = [
        'media_id',
        'usage_type',
        'entity_type',
        'entity_id',
        'field_name',
        'sort_order',
    ];

    protected $casts = [
        'media_id' => 'integer',
        'entity_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}