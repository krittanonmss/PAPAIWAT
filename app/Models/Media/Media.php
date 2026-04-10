<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'disk',
        'directory',
        'filename',
        'original_filename',
        'extension',
        'mime_type',
        'media_type',
        'file_size',
        'width',
        'height',
        'title',
        'alt_text',
        'caption',
        'description',
        'checksum',
        'visibility',
        'upload_status',
        'uploaded_by_admin_id',
        'uploaded_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'uploaded_by_admin_id' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    public function uploadedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by_admin_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MediaVariant::class, 'media_id');
    }

    public function folderItems(): HasMany
    {
        return $this->hasMany(MediaFolderItem::class, 'media_id');
    }

    public function folders(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaFolder::class,
            'media_folder_items',
            'media_id',
            'media_folder_id'
        )->withPivot('sort_order')->withTimestamps();
    }

    public function tagItems(): HasMany
    {
        return $this->hasMany(MediaTagItem::class, 'media_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaTag::class,
            'media_tag_items',
            'media_id',
            'media_tag_id'
        )->withTimestamps();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(MediaUsage::class, 'media_id');
    }

    public function temples(): BelongsToMany
    {
        return $this->belongsToMany(Temple::class, 'temple_media')
            ->withPivot(['collection', 'alt_text', 'caption', 'is_cover', 'sort_order'])
            ->withTimestamps();
    }

    public function templeMedia(): HasMany
    {
        return $this->hasMany(TempleMedia::class);
    }

    public function templesAsCover(): HasMany
    {
        return $this->hasMany(Temple::class, 'cover_media_id');
    }

    public function templeHighlights(): HasMany
    {
        return $this->hasMany(TempleHighlight::class);
    }
}