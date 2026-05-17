<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminSession;
use App\Models\Admin\AuditLog;
use App\Models\Admin\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Admin::query()->with('role');

        if ($request->string('status')->toString() === 'deleted') {
            $query->onlyTrashed();
        }

        $query->latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('status') && $request->string('status')->toString() !== 'deleted') {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->integer('role_id'));
        }

        $admins = $query->paginate(10)->withQueryString();
        $roles = Role::query()->orderByDesc('level')->orderBy('name')->get();

        return view('admin.user-management.index', compact('admins', 'roles'));
    }

    public function create(): View
    {
        $roles = $this->assignableRoles(auth('admin')->user());

        return view('admin.user-management.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:admins,username'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'string', Password::min(12)->letters()->mixedCase()->numbers(), 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $actor = auth('admin')->user();
        $role = Role::query()->findOrFail($validated['role_id']);

        if (! $this->canAssignRole($actor, $role)) {
            return $this->deny('ไม่สามารถสร้างผู้ใช้ด้วยบทบาทระดับเท่ากันหรือสูงกว่าคุณได้');
        }

        DB::transaction(function () use ($request, $validated): void {
            $created = Admin::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'status' => $validated['status'],
                'phone' => $validated['phone'] ?? null,
            ]);

            $this->writeAuditLog($request, 'admin.created', $created, null, $this->adminAuditData($created));
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'สร้างผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function show(Admin $admin): View
    {
        $admin->load('role');

        return view('admin.user-management.show', compact('admin'));
    }

    public function edit(Admin $admin): View
    {
        $this->ensureCanManageTarget(auth('admin')->user(), $admin);

        $roles = $this->assignableRoles(auth('admin')->user());

        return view('admin.user-management.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:admins,username,'.$admin->id],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'password' => ['nullable', 'string', Password::min(12)->letters()->mixedCase()->numbers(), 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'phone' => ['nullable', 'string', 'max:50'],
            'current_password' => ['nullable', 'string'],
        ]);

        $actor = auth('admin')->user();
        $this->ensureCanManageTarget($actor, $admin);

        $newRole = Role::query()->findOrFail($validated['role_id']);

        if (! $this->canAssignRole($actor, $newRole)) {
            return $this->deny('ไม่สามารถกำหนดบทบาทระดับเท่ากันหรือสูงกว่าคุณได้');
        }

        $sensitiveChanges = $this->hasSensitiveUserChanges($admin, $validated);

        if ($sensitiveChanges) {
            $this->confirmCurrentPassword($request);
        }

        if (
            $admin->isSuperAdmin()
            && (
                (int) $admin->role_id !== (int) $validated['role_id']
                || $validated['status'] !== 'active'
            )
            && $this->isLastActiveSuperAdmin($admin)
        ) {
            return $this->deny('ไม่สามารถลดบทบาทหรือปิดใช้งาน Super Admin คนสุดท้ายได้');
        }

        DB::transaction(function () use ($request, $admin, $validated, $sensitiveChanges): void {
            $oldData = $this->adminAuditData($admin);

            $admin->username = $validated['username'];
            $admin->email = $validated['email'];
            $admin->role_id = $validated['role_id'];
            $admin->status = $validated['status'];
            $admin->phone = $validated['phone'] ?? null;

            if (! empty($validated['password'])) {
                $admin->password_hash = Hash::make($validated['password']);
            }

            $admin->save();
            $admin->refresh();

            if ($sensitiveChanges) {
                $this->revokeTargetSessions($admin);
            }

            $newData = $this->adminAuditData($admin);

            $this->writeAuditLog($request, 'admin.updated', $admin, $oldData, $newData);

            if (($oldData['role_id'] ?? null) !== ($newData['role_id'] ?? null)) {
                $this->writeAuditLog($request, 'admin.role_changed', $admin, ['role_id' => $oldData['role_id']], ['role_id' => $newData['role_id']]);
            }

            if (($oldData['status'] ?? null) !== ($newData['status'] ?? null)) {
                $this->writeAuditLog($request, 'admin.status_changed', $admin, ['status' => $oldData['status']], ['status' => $newData['status']]);
            }

            if (! empty($validated['password'])) {
                $this->writeAuditLog($request, 'admin.password_changed', $admin, null, ['password_changed' => true]);
            }
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'อัปเดตผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function updateStatus(Request $request, Admin $admin): RedirectResponse
    {
        if ($admin->id === auth('admin')->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'ไม่สามารถเปลี่ยนสถานะบัญชีของตัวเองได้');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive'],
            'current_password' => ['required', 'string'],
        ]);

        $actor = auth('admin')->user();
        $this->ensureCanManageTarget($actor, $admin);
        $this->confirmCurrentPassword($request);

        if ($admin->isSuperAdmin() && $validated['status'] !== 'active' && $this->isLastActiveSuperAdmin($admin)) {
            return $this->deny('ไม่สามารถปิดใช้งาน Super Admin คนสุดท้ายได้');
        }

        DB::transaction(function () use ($request, $admin, $validated): void {
            $oldData = ['status' => $admin->status];

            $admin->update([
                'status' => $validated['status'],
            ]);

            $this->revokeTargetSessions($admin);
            $this->writeAuditLog($request, 'admin.status_changed', $admin, $oldData, ['status' => $validated['status']]);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'อัปเดตสถานะผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function bulkUpdateRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'admin_ids' => ['required', 'array', 'min:1'],
            'admin_ids.*' => ['integer', 'exists:admins,id'],
            'role_id' => ['required', 'exists:roles,id'],
            'current_password' => ['required', 'string'],
        ]);

        $actor = auth('admin')->user();
        $newRole = Role::query()->findOrFail($validated['role_id']);

        $this->confirmCurrentPassword($request);

        if (! $this->canAssignRole($actor, $newRole)) {
            return $this->deny('ไม่สามารถกำหนดบทบาทระดับเท่ากันหรือสูงกว่าคุณได้');
        }

        $updatedCount = 0;

        DB::transaction(function () use ($request, $actor, $validated, &$updatedCount): void {
            $targets = Admin::query()
                ->with('role')
                ->whereIn('id', $validated['admin_ids'])
                ->where('id', '!=', $actor->id)
                ->get();

            foreach ($targets as $target) {
                if (! $this->canManageTarget($actor, $target)) {
                    continue;
                }

                if ($target->isSuperAdmin() && (int) $target->role_id !== (int) $validated['role_id'] && $this->isLastActiveSuperAdmin($target)) {
                    continue;
                }

                $oldData = ['role_id' => $target->role_id];
                $target->update(['role_id' => $validated['role_id']]);

                $this->revokeTargetSessions($target);
                $this->writeAuditLog($request, 'admin.role_changed', $target, $oldData, ['role_id' => $validated['role_id']]);

                $updatedCount++;
            }
        });

        if ($updatedCount === 0) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'ไม่สามารถอัปเดตได้ กรุณาเลือกผู้ใช้งานอื่นที่ไม่ใช่บัญชีของคุณเอง');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'อัปเดตบทบาทผู้ใช้งานที่เลือกเรียบร้อยแล้ว');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'admin_ids' => ['required', 'array', 'min:1'],
            'admin_ids.*' => ['integer', 'exists:admins,id'],
            'status' => ['required', 'in:active,inactive'],
            'current_password' => ['required', 'string'],
        ]);

        $actor = auth('admin')->user();
        $this->confirmCurrentPassword($request);

        $updatedCount = 0;

        DB::transaction(function () use ($request, $actor, $validated, &$updatedCount): void {
            $targets = Admin::query()
                ->with('role')
                ->whereIn('id', $validated['admin_ids'])
                ->where('id', '!=', $actor->id)
                ->get();

            foreach ($targets as $target) {
                if (! $this->canManageTarget($actor, $target)) {
                    continue;
                }

                if ($target->isSuperAdmin() && $validated['status'] !== 'active' && $this->isLastActiveSuperAdmin($target)) {
                    continue;
                }

                $oldData = ['status' => $target->status];
                $target->update(['status' => $validated['status']]);

                $this->revokeTargetSessions($target);
                $this->writeAuditLog($request, 'admin.status_changed', $target, $oldData, ['status' => $validated['status']]);

                $updatedCount++;
            }
        });

        if ($updatedCount === 0) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'ไม่สามารถอัปเดตได้ กรุณาเลือกผู้ใช้งานอื่นที่ไม่ใช่บัญชีของคุณเอง');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'อัปเดตสถานะผู้ใช้งานที่เลือกเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, Admin $admin): RedirectResponse
    {
        if ($admin->id === auth('admin')->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        $actor = auth('admin')->user();
        $this->ensureCanManageTarget($actor, $admin);
        $this->confirmCurrentPassword($request);

        if ($admin->isSuperAdmin() && $this->isLastActiveSuperAdmin($admin)) {
            return $this->deny('ไม่สามารถลบ Super Admin คนสุดท้ายได้');
        }

        DB::transaction(function () use ($request, $admin): void {
            $oldData = $this->adminAuditData($admin);

            $this->revokeTargetSessions($admin);
            $admin->delete();

            $this->writeAuditLog($request, 'admin.deleted', $admin, $oldData, ['deleted' => true]);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function restore(Request $request, int $admin): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        $target = Admin::onlyTrashed()->with('role')->findOrFail($admin);
        $actor = auth('admin')->user();

        $this->ensureCanManageTarget($actor, $target);
        $this->confirmCurrentPassword($request);

        DB::transaction(function () use ($request, $target): void {
            $target->restore();
            $target->refresh();

            $this->writeAuditLog($request, 'admin.restored', $target, ['deleted' => true], $this->adminAuditData($target));
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'กู้คืนผู้ใช้งานเรียบร้อยแล้ว');
    }

    private function assignableRoles(Admin $actor)
    {
        return Role::query()
            ->when(! $actor->isSuperAdmin(), fn ($query) => $query->where('level', '<', $actor->roleLevel()))
            ->orderByDesc('level')
            ->orderBy('name')
            ->get();
    }

    private function canAssignRole(Admin $actor, Role $role): bool
    {
        return $actor->isSuperAdmin() || $role->level < $actor->roleLevel();
    }

    private function canManageTarget(Admin $actor, Admin $target): bool
    {
        if ($actor->id === $target->id) {
            return false;
        }

        return $actor->isSuperAdmin() || $target->roleLevel() < $actor->roleLevel();
    }

    private function ensureCanManageTarget(Admin $actor, Admin $target): void
    {
        if (! $this->canManageTarget($actor, $target)) {
            throw ValidationException::withMessages([
                'user' => 'ไม่สามารถจัดการผู้ใช้ระดับเท่ากันหรือสูงกว่าคุณได้',
            ]);
        }
    }

    private function confirmCurrentPassword(Request $request): void
    {
        $currentPassword = (string) $request->input('current_password');
        $actor = auth('admin')->user();

        if (! $actor || ! Hash::check($currentPassword, $actor->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง',
            ]);
        }
    }

    private function hasSensitiveUserChanges(Admin $admin, array $validated): bool
    {
        return (int) $admin->role_id !== (int) $validated['role_id']
            || $admin->status !== $validated['status']
            || ! empty($validated['password']);
    }

    private function isLastActiveSuperAdmin(Admin $admin): bool
    {
        return Admin::query()
            ->where('status', 'active')
            ->where('id', '!=', $admin->id)
            ->whereHas('role', fn ($query) => $query->where('role_key', 'super_admin'))
            ->doesntExist();
    }

    private function revokeTargetSessions(Admin $admin): void
    {
        AdminSession::query()
            ->where('admin_id', $admin->id)
            ->delete();
    }

    private function writeAuditLog(Request $request, string $action, Admin $target, ?array $oldData, ?array $newData): void
    {
        AuditLog::query()->create([
            'action' => $action,
            'table_name' => 'admins',
            'record_id' => $target->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'performed_by' => auth('admin')->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }

    private function adminAuditData(Admin $admin): array
    {
        return [
            'id' => $admin->id,
            'username' => $admin->username,
            'email' => $admin->email,
            'role_id' => $admin->role_id,
            'role_key' => $admin->role?->role_key,
            'status' => $admin->status,
            'phone' => $admin->phone,
            'deleted_at' => $admin->deleted_at?->toDateTimeString(),
        ];
    }

    private function deny(string $message): RedirectResponse
    {
        return redirect()
            ->route('admin.users.index')
            ->with('error', $message);
    }
}
