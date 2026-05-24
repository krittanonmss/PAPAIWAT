<?php

namespace App\Models\Interaction;

use App\Models\Content\Temple\Temple;
use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'moderation_reason',
        'moderation_note',
        'moderated_by_admin_id',
        'moderated_at',
        'ip_hash',
        'user_agent_hash',
        'approved_at',
    ];

    protected $casts = [
        'temple_id' => 'integer',
        'anonymous_visitor_id' => 'integer',
        'rating' => 'integer',
        'report_count' => 'integer',
        'moderated_by_admin_id' => 'integer',
        'moderated_at' => 'datetime',
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

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'moderated_by_admin_id');
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
