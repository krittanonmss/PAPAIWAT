<?php

namespace App\Models\Content\Layout;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSectionVersion extends Model
{
    protected $fillable = [
        'page_section_id',
        'version_name',
        'snapshot',
        'created_by_admin_id',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
