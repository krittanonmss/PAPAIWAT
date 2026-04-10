<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Permission\StorePermissionRequest;
use App\Http\Requests\Admin\Permission\UpdatePermissionRequest;
use App\Models\Admin\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    private const GROUP_OPTIONS = [
        'users' => 'ผู้ใช้งาน',
        'roles' => 'บทบาท',
        'permissions' => 'สิทธิ์',
        'temples' => 'วัด',
        'media' => 'สื่อ',
        'settings' => 'ตั้งค่าระบบ',
        'dashboard' => 'แดชบอร์ด',
    ];

    private const ACTION_OPTIONS = [
        'view' => 'ดูข้อมูล',
        'create' => 'สร้าง',
        'update' => 'แก้ไข',
        'delete' => 'ลบ',
        'manage' => 'จัดการ',
        'publish' => 'เผยแพร่',
        'approve' => 'อนุมัติ',
        'permissions' => 'จัดการสิทธิ์',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $groupKey = trim((string) $request->query('group_key'));

        $permissions = Permission::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($groupKey !== '', function ($query) use ($groupKey) {
                $query->where('group_key', $groupKey);
            })
            ->orderBy('group_key')
            ->orderBy('key')
            ->paginate(10)
            ->withQueryString();

        return view('admin.permission.index', [
            'permissions' => $permissions,
            'groupOptions' => self::GROUP_OPTIONS,
            'search' => $search,
            'selectedGroupKey' => $groupKey,
        ]);
    }

    public function create(): View
    {
        return view('admin.permission.create', [
            'groupOptions' => self::GROUP_OPTIONS,
            'actionOptions' => self::ACTION_OPTIONS,
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        Permission::query()->create($request->validated());

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'สร้าง permission เรียบร้อยแล้ว');
    }

    public function edit(Permission $permission): View
    {
        return view('admin.permission.edit', [
            'permission' => $permission,
            'groupOptions' => self::GROUP_OPTIONS,
            'actionOptions' => self::ACTION_OPTIONS,
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $permission->update($request->validated());

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'อัปเดต permission เรียบร้อยแล้ว');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if ($permission->rolePermissions()->exists()) {
            return redirect()
                ->route('admin.permissions.index')
                ->with('error', 'ไม่สามารถลบ permission นี้ได้ เนื่องจากมีการใช้งานกับ role อยู่');
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'ลบ permission เรียบร้อยแล้ว');
    }
}