<x-layouts.admin title="User Management">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
                <p class="text-sm text-slate-600">Manage administrator accounts.</p>
            </div>

            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Create User
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label for="search" class="mb-1 block text-sm font-medium text-slate-700">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        placeholder="Username, email, phone">
                </div>

                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select
                        name="status"
                        id="status"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">All</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div>
                    <label for="role_id" class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                    <select
                        name="role_id"
                        id="role_id"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">All</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) request('role_id') === (string) $role->id)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Filter
                    </button>

                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Username</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Email</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Role</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Last Login</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($admins as $admin)
                            <tr>
                                <td class="px-4 py-3 text-slate-800">{{ $admin->username }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $admin->email }}</td>
                                <td class="px-4 py-3 text-slate-800">{{ $admin->role?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $admin->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($admin->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-800">
                                    {{ $admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.users.show', $admin) }}"
                                           class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                            View
                                        </a>
                                        <a href="{{ route('admin.users.edit', $admin) }}"
                                           class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" onsubmit="return confirm('Delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $admins->links() }}
            </div>
        </div>
    </div>
</x-layouts.dashboard>