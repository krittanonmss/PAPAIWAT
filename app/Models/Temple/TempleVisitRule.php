<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleVisitRule extends Model
{
    protected $fillable = [
        'temple_id',
        'title',
        'description',
        'rule_type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }
}