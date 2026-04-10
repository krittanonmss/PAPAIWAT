<?php

namespace App\Models\Admin;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'action',
        'table_name',
        'record_id',
        'old_data',
        'new_data',
        'performed_by',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function performer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'performed_by');
    }
}