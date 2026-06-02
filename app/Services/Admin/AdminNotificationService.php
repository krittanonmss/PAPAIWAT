<?php

namespace App\Services\Admin;

use App\Mail\AdminNotificationMail;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminNotification;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Mail;

class AdminNotificationService
{
    public function notifyAdmins(string $type, string $title, string $message): void
    {
        Admin::query()
            ->where('status', 'active')
            ->get()
            ->each(fn (Admin $admin) => $this->notify($admin, $type, $title, $message, false));

        $this->notifyCentralModerationEmail($type, $title, $message);
    }

    public function notifyAdminsWithPermission(string $permission, string $type, string $title, string $message): void
    {
        Admin::query()
            ->where('status', 'active')
            ->with('role.permissions')
            ->get()
            ->filter(fn (Admin $admin) => $admin->hasPermission($permission))
            ->each(fn (Admin $admin) => $this->notify($admin, $type, $title, $message, false));

        $this->notifyCentralModerationEmail($type, $title, $message);
    }

    public function notify(Admin $admin, string $type, string $title, string $message, bool $sendCentralModerationEmail = true): void
    {
        $preferences = app(AdminPreferenceService::class)->forAdmin($admin);

        if ($type === 'moderation' && ! (bool) ($preferences['notifications.moderation_alerts'] ?? true)) {
            return;
        }

        if ((bool) ($preferences['notifications.in_app'] ?? true)) {
            AdminNotification::query()->create([
                'admin_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
                'created_at' => now(),
            ]);
        }

        if ((bool) ($preferences['notifications.email'] ?? false) && filled($admin->email)) {
            Mail::to($admin->email)->queue(new AdminNotificationMail($title, $message));
        }

        if ($sendCentralModerationEmail) {
            $this->notifyCentralModerationEmail($type, $title, $message, $admin->email);
        }
    }

    private function notifyCentralModerationEmail(string $type, string $title, string $message, ?string $exceptEmail = null): void
    {
        $moderationEmail = SiteSettings::get('moderation', 'notification_email');
        if ($type === 'moderation' && filled($moderationEmail) && $moderationEmail !== $exceptEmail) {
            Mail::to($moderationEmail)->queue(new AdminNotificationMail($title, $message));
        }
    }
}
