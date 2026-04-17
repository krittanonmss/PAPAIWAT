<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleStat extends Model
{
    protected $table = 'temple_stats';

    public $timestamps = false;

    protected $fillable = [
        'temple_id',
        'review_count',
        'average_rating',
        'favorite_count',
        'score',
        'updated_at',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'review_count' => 'integer',
        'average_rating' => 'decimal:2',
        'favorite_count' => 'integer',
        'score' => 'decimal:2',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }
}