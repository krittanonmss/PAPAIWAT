<?php

namespace App\Models\Content\Layout;

use App\Models\Admin\Admin;
use App\Models\Content\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $table = 'menu_items';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'slug',
        'menu_item_type',
        'route_name',
        'route_params',
        'page_id',
        'content_id',
        'url',
        'external_url',
        'anchor',
        'target',
        'rel',
        'icon',
        'css_class',
        'is_enabled',
        'sort_order',
        'starts_at',
        'ends_at',
        'description',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'route_params' => 'array',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->enabled()
            ->active()
            ->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function updatedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeActive($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}