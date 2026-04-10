<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFolder extends Model
{
    use SoftDeletes;

    protected $table = 'media_folders';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'created_by_admin_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'created_by_admin_id' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function folderItems(): HasMany
    {
        return $this->hasMany(MediaFolderItem::class, 'media_folder_id');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(
            Media::class,
            'media_folder_items',
            'media_folder_id',
            'media_id'
        )->withPivot('sort_order')->withTimestamps();
    }
}