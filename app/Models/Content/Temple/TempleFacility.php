<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleFacility extends Model
{
    protected $table = 'temple_facilities';

    protected $fillable = [
        'temple_id',
        'facility_id',
        'value',
        'note',
        'sort_order',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'facility_id' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }
}