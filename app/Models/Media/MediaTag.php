<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaTag extends Model
{
    protected $table = 'media_tags';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function tagItems(): HasMany
    {
        return $this->hasMany(MediaTagItem::class, 'media_tag_id');
    }

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