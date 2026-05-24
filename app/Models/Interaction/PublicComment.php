<?php

namespace App\Models\Interaction;

use App\Models\Admin\Admin;
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
        'moderation_reason',
        'moderation_note',
        'moderated_by_admin_id',
        'moderated_at',
        'ip_hash',
        'user_agent_hash',
        'approved_at',
    ];

    protected $casts = [
        'anonymous_visitor_id' => 'integer',
        'commentable_id' => 'integer',
        'parent_id' => 'integer',
        'report_count' => 'integer',
        'moderated_by_admin_id' => 'integer',
        'moderated_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnonymousVisitor::class, 'anonymous_visitor_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'moderated_by_admin_id');
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

    public function reports(): HasMany
    {
        return $this->hasMany(InteractionReport::class, 'reportable_id')
            ->where('reportable_type', self::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}
