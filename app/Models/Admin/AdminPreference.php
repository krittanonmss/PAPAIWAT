<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPreference extends Model
{
    protected $fillable = [
        'admin_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
