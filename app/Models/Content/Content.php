<?php

namespace App\Models\Content;

use App\Models\Admin\Admin;
use App\Models\Content\Article\Article;
use App\Models\Content\Media\Media;
use App\Models\Content\Media\MediaUsage;
use App\Models\Content\Temple\Temple;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_type',
        'title',
        'slug',
        'excerpt',
        'description',
        'status',
        'is_featured',
        'is_popular',
        'meta_title',
        'meta_description',
        'published_at',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'published_at' => 'datetime',
        'created_by_admin_id' => 'integer',
        'updated_by_admin_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(
            Category::class,
            'categorizable',
            'categorizables'
        )->withPivot([
            'is_primary',
            'sort_order',
            'created_at',
        ]);
    }

    public function media(): MorphToMany
    {
        return $this->morphToMany(
            Media::class,
            'entity',
            'media_usages',
            'entity_id',
            'media_id'
        )->withPivot([
            'role_key',
            'sort_order',
            'created_by_admin_id',
            'created_at',
            'updated_at',
        ]);
    }

    public function mediaUsages(): MorphMany
    {
        return $this->morphMany(
            MediaUsage::class,
            'entity',
            'entity_type',
            'entity_id'
        )->orderBy('sort_order')
            ->orderBy('id');
    }

    public function temple(): HasOne
    {
        return $this->hasOne(Temple::class, 'content_id');
    }

    public function article(): HasOne
    {
        return $this->hasOne(Article::class, 'content_id');
    }

    public function template()
    {
        return $this->belongsTo(
            \App\Models\Content\Layout\Template::class,
            'template_id'
        );
    }
}