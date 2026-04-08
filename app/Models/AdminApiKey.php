<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminApiKey extends Model
{
    protected $table = 'admin_api_keys';

    protected $fillable = [
        'admin_id',
        'name',
        'api_key_hash',
        'last_used_at',
        'is_active',
        'expires_at',
        'revoked_at',
    ];

    protected $hidden = [
        'api_key_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}