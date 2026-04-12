<?php

namespace App\Models\Content;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'type_key',
        'level',
        'sort_order',
        'status',
        'is_featured',
        'meta_title',
        'meta_description',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'level' => 'integer',
        'sort_order' => 'integer',
        'is_featured' => 'boolean',
        'created_by_admin_id' => 'integer',
        'updated_by_admin_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    // public function temples(): MorphToMany
    // {
    //     return $this->morphedByMany(
    //         \App\Models\Temple\Temple::class,
    //         'categorizable',
    //         'categorizables'
    //     )->withPivot([
    //         'is_primary',
    //         'sort_order',
    //         'created_at',
    //     ]);
    // }

    // public function articles(): MorphToMany
    // {
    //     return $this->morphedByMany(
    //         \App\Models\Article\Article::class,
    //         'categorizable',
    //         'categorizables'
    //     )->withPivot([
    //         'is_primary',
    //         'sort_order',
    //         'created_at',
    //     ]);
    // }
}