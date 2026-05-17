<?php

namespace App\Models\Content;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentVersion extends Model
{
    protected $fillable = [
        'content_id',
        'content_type',
        'version_name',
        'snapshot',
        'created_by_admin_id',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
