<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'role_id',
        'status',
        'avatar_media_id',
        'phone',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AdminSession::class, 'admin_id');
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class, 'admin_id');
    }

    public function performedAuditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'performed_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class, 'admin_id');
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(AdminApiKey::class, 'admin_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(AdminActivityLog::class, 'admin_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === 'super_admin';
    }

    public function hasPermission(string $permissionKey): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->role) {
            return false;
        }

        return $this->role->permissions()
            ->where('key', $permissionKey)
            ->exists();
    }

    public function uploadedMedia(): HasMany
    {
        return $this->hasMany(Media::class, 'uploaded_by_admin_id');
    }

    public function createdMediaFolders(): HasMany
    {
        return $this->hasMany(MediaFolder::class, 'created_by_admin_id');
    }

    public function createdTemples(): HasMany
    {
        return $this->hasMany(Temple::class, 'created_by_admin_id');
    }

    public function updatedTemples(): HasMany
    {
        return $this->hasMany(Temple::class, 'updated_by_admin_id');
    }
}