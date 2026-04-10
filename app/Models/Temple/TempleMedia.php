<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleMedia extends Model
{
    protected $table = 'temple_media';

    protected $fillable = [
        'temple_id',
        'media_id',
        'collection',
        'alt_text',
        'caption',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}