<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Role::query()->withCount('admins')->latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $roles = $query->paginate(10)->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string'],
        ]);

        Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'description' => ['nullable', 'string'],
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $hasAdmins = Admin::withTrashed()
            ->where('role_id', $role->id)
            ->exists();

        if ($hasAdmins) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'ไม่สามารถลบบทบาทนี้ได้ เนื่องจากยังมีผู้ดูแลระบบผูกอยู่ในระบบ');
        }

        if ($role->rolePermissions()->exists()) {
            $role->rolePermissions()->delete();
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'ลบบทบาทเรียบร้อยแล้ว');
    }
}