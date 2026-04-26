<?php

namespace App\Models\Content\Layout;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menus';

    protected $fillable = [
        'name',
        'slug',
        'location_key',
        'description',
        'status',
        'is_default',
        'sort_order',
        'created_by_admin_id',
        'updated_by_admin_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->orderBy('sort_order');
    }

    public function rootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->orderBy('sort_order');
    }

    public function enabledItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->enabled()
            ->active()
            ->orderBy('sort_order');
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function updatedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLocationKey($query, string $locationKey)
    {
        return $query->where('location_key', $locationKey);
    }
}