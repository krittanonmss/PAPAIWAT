<?php

namespace App\Models\Content\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaTag extends Model
{
    protected $table = 'media_tags';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(
            Media::class,
            'media_tag_items',
            'media_tag_id',
            'media_id'
        )->withTimestamps();
    }
}