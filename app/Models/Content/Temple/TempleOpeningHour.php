<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleOpeningHour extends Model
{
    protected $table = 'temple_opening_hours';

    protected $fillable = [
        'temple_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
        'note',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'day_of_week' => 'integer',
        'is_closed' => 'boolean',
        'open_time' => 'string',
        'close_time' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }
}
