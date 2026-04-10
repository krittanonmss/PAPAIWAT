<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleFee extends Model
{
    protected $fillable = [
        'temple_id',
        'fee_type',
        'label',
        'price',
        'currency',
        'note',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }
}