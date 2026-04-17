<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use SoftDeletes;

    protected $table = 'facilities';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'type_key',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function templeFacilities(): HasMany
    {
        return $this->hasMany(TempleFacility::class, 'facility_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}