<?php

namespace App\Models\Content\Layout;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateVersion extends Model
{
    protected $fillable = [
        'template_id',
        'version_name',
        'snapshot',
        'created_by_admin_id',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
