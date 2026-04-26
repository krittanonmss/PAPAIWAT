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
        'status',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}