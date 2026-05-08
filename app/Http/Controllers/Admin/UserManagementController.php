<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Models\Admin\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Admin::query()
            ->with('role')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->integer('role_id'));
        }

        $admins = $query->paginate(10)->withQueryString();
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.user-management.index', compact('admins', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::query()->orderBy('name')->get();

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

        Admin::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'status' => $validated['status'],
            'phone' => $validated['phone'] ?? null,
        ]);

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
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.user-management.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:admins,username,'.$admin->id],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'password' => ['nullable', 'string', Password::min(12)->letters()->mixedCase()->numbers(), 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $admin->username = $validated['username'];
        $admin->email = $validated['email'];
        $admin->role_id = $validated['role_id'];
        $admin->phone = $validated['phone'] ?? null;

        if (! empty($validated['password'])) {
            $admin->password_hash = Hash::make($validated['password']);
        }

        $admin->save();

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
        ]);

        $admin->update([
            'status' => $validated['status'],
        ]);

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
        ]);

        $updatedCount = Admin::query()
            ->whereIn('id', $validated['admin_ids'])
            ->where('id', '!=', auth('admin')->id())
            ->update([
                'role_id' => $validated['role_id'],
            ]);

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
        ]);

        $updatedCount = Admin::query()
            ->whereIn('id', $validated['admin_ids'])
            ->where('id', '!=', auth('admin')->id())
            ->update([
                'status' => $validated['status'],
            ]);

        if ($updatedCount === 0) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'ไม่สามารถอัปเดตได้ กรุณาเลือกผู้ใช้งานอื่นที่ไม่ใช่บัญชีของคุณเอง');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'อัปเดตสถานะผู้ใช้งานที่เลือกเรียบร้อยแล้ว');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        if ($admin->id === auth('admin')->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $admin->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }
}
