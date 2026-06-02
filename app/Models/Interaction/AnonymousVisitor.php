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

    public function reports(): HasMany
    {
        return $this->hasMany(InteractionReport::class);
    }

    public function isBanned(): bool
    {
        $hasActiveBan = InteractionBan::query()
            ->where('ban_type', 'visitor')
            ->where('value_hash', hash('sha256', $this->visitor_uuid))
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($hasActiveBan) {
            if ($this->status !== 'banned') {
                $this->update(['status' => 'banned', 'banned_at' => $this->banned_at ?? now()]);
            }

            return true;
        }

        if ($this->status === 'banned') {
            $this->update(['status' => 'active', 'banned_at' => null]);
        }

        return false;
    }
}
