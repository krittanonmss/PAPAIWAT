<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleCategoryItem extends Model
{
    protected $fillable = [
        'temple_id',
        'temple_category_id',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TempleCategory::class, 'temple_category_id');
    }
}