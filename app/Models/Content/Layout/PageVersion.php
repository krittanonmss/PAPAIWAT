<?php

namespace App\Models\Content\Layout;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVersion extends Model
{
    protected $fillable = [
        'page_id',
        'version_name',
        'snapshot',
        'created_by_admin_id',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}