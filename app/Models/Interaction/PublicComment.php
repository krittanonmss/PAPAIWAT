<?php

namespace App\Models\Interaction;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'anonymous_visitor_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'display_name',
        'body',
        'status',
        'report_count',
        'ip_hash',
        'user_agent_hash',
        'approved_at',
    ];

    protected $casts = [
        'anonymous_visitor_id' => 'integer',
        'commentable_id' => 'integer',
        'parent_id' => 'integer',
        'report_count' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnonymousVisitor::class, 'anonymous_visitor_id');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}
