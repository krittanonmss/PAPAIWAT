<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleHighlight extends Model
{
    protected $fillable = [
        'temple_id',
        'title',
        'description',
        'media_id',
        'sort_order',
    ];

    protected $casts = [
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