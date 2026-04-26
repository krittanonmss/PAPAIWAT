<?php

namespace App\Models\Content\Layout;

use App\Models\Admin\Admin;
use App\Models\Content\Media\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'template_id',
        'title',
        'slug',
        'page_type',
        'status',
        'is_homepage',
        'sort_order',
        'excerpt',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image_media_id',
        'published_at',
        'unpublished_at',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'is_homepage' => 'boolean',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
        'unpublished_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class);
    }

    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_image_media_id');
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function updatedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('unpublished_at')
                    ->orWhere('unpublished_at', '>=', now());
            });
    }
}