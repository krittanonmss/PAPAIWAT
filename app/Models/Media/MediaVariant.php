<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaVariant extends Model
{
    protected $table = 'media_variants';

    protected $fillable = [
        'media_id',
        'variant_name',
        'disk',
        'directory',
        'filename',
        'extension',
        'mime_type',
        'file_size',
        'width',
        'height',
        'processing_status',
        'generated_at',
    ];

    protected $casts = [
        'media_id' => 'integer',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'generated_at' => 'datetime',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}