<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleOpeningHour extends Model
{
    protected $fillable = [
        'temple_id',
        'day_of_week',
        'is_closed',
        'open_time',
        'close_time',
        'note',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_closed' => 'boolean',
        'open_time' => 'datetime:H:i:s',
        'close_time' => 'datetime:H:i:s',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }
}