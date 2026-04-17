<?php

namespace App\Models\Content\Temple;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempleVisitRule extends Model
{
    protected $table = 'temple_visit_rules';

    protected $fillable = [
        'temple_id',
        'rule_text',
        'sort_order',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class, 'temple_id');
    }
}