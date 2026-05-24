<?php

namespace App\Models\Interaction;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
