<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleFee extends Model
{
    protected $table = 'temple_fees';

    protected $fillable = [
        'temple_id',
        'fee_type',
        'label',
        'amount',
        'currency',
        'note',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }
}