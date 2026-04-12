<?php

namespace App\Models\Content\Media;

use App\Models\Admin\Admin;
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
        'media_folder_id',
        'sort_order',
        'disk',
        'directory',
        'filename',
        'path',
        'original_filename',
        'extension',
        'mime_type',
        'media_type',
        'file_size',
        'width',
        'height',
        'duration_seconds',
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
        'media_folder_id' => 'integer',
        'sort_order' => 'integer',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration_seconds' => 'integer',
        'uploaded_by_admin_id' => 'integer',
        'uploaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'media_folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by_admin_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MediaVariant::class, 'media_id')
            ->orderBy('id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(MediaUsage::class, 'media_id')
            ->orderBy('sort_order')
            ->orderBy('id');
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
}