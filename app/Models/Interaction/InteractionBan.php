<?php

namespace App\Models\Interaction;

use Illuminate\Database\Eloquent\Model;

class InteractionBan extends Model
{
    protected $fillable = [
        'ban_type',
        'value_hash',
        'reason',
        'created_by_admin_id',
        'expires_at',
    ];

    protected $casts = [
        'created_by_admin_id' => 'integer',
        'expires_at' => 'datetime',
    ];
}
