<?php

namespace App\Models\Interaction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnonymousVisitor extends Model
{
    protected $fillable = [
        'visitor_uuid',
        'ip_hash',
        'user_agent_hash',
        'status',
        'banned_at',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'banned_at' => 'datetime',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function templeReviews(): HasMany
    {
        return $this->hasMany(TempleReview::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PublicComment::class);
    }
}
