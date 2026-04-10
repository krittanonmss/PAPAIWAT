<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TempleCategory extends Model
{
    protected $fillable = [
        'name',
        'key',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function categoryItems(): HasMany
    {
        return $this->hasMany(TempleCategoryItem::class);
    }

    public function temples(): BelongsToMany
    {
        return $this->belongsToMany(Temple::class, 'temple_category_items')
            ->withTimestamps();
    }
}