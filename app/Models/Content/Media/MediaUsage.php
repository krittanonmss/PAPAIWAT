<?php

namespace App\Models\Content\Media;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaUsage extends Model
{
    protected $table = 'media_usages';

    protected $fillable = [
        'media_id',
        'entity_type',
        'entity_id',
        'role_key',
        'sort_order',
        'created_by_admin_id',
    ];

    protected $casts = [
        'media_id' => 'integer',
        'entity_id' => 'integer',
        'sort_order' => 'integer',
        'created_by_admin_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}