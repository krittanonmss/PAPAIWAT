<?php

namespace App\Models\Interaction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InteractionReport extends Model
{
    protected $fillable = [
        'anonymous_visitor_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'ip_hash',
        'user_agent_hash',
    ];

    protected $casts = [
        'anonymous_visitor_id' => 'integer',
        'reportable_id' => 'integer',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnonymousVisitor::class, 'anonymous_visitor_id');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
