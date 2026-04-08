<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSession extends Model
{
    protected $table = 'admin_sessions';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'admin_id',
        'session_token_hash',
        'ip_address',
        'user_agent',
        'last_seen_at',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}