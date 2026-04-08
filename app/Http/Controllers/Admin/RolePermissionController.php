<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    public function edit(Role $role): View
    {
        $permissions = Permission::query()
            ->orderBy('group_key')
            ->orderBy('name')
            ->get()
            ->groupBy('group_key');

        $role->load('permissions');

        return view('admin.role-permissions.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->permissions()->sync($validated['permission_ids'] ?? []);

        return redirect()
            ->route('admin.roles.permissions.edit', $role)
            ->with('success', 'Permissions updated successfully.');
    }
}