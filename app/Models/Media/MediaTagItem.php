<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaTagItem extends Model
{
    protected $table = 'media_tag_items';

    protected $fillable = [
        'media_tag_id',
        'media_id',
    ];

    protected $casts = [
        'media_tag_id' => 'integer',
        'media_id' => 'integer',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(MediaTag::class, 'media_tag_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}