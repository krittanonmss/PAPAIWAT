<?php

namespace App\Models\Content\Layout;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'key',
        'description',
        'view_path',
        'template_type',
        'content_type',
        'schema',
        'status',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'schema' => 'array',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TemplateVersion::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
