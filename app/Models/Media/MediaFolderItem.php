<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaFolderItem extends Model
{
    protected $table = 'media_folder_items';

    protected $fillable = [
        'media_folder_id',
        'media_id',
        'sort_order',
    ];

    protected $casts = [
        'media_folder_id' => 'integer',
        'media_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'media_folder_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}