<?php

namespace App\Models\Interaction;

use App\Models\Content\Temple\Temple;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempleReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'temple_id',
        'anonymous_visitor_id',
        'rating',
        'display_name',
        'comment',
        'status',
        'report_count',
        'ip_hash',
        'user_agent_hash',
        'approved_at',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'anonymous_visitor_id' => 'integer',
        'rating' => 'integer',
        'report_count' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnonymousVisitor::class, 'anonymous_visitor_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}
