<x-layouts.admin title="User Detail">
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">User Detail</h1>
                <p class="text-sm text-slate-600">View administrator account information.</p>
            </div>

            <a href="{{ route('admin.users.edit', $admin) }}"
               class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                Edit User
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="divide-y divide-slate-200">
                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-500">Username</div>
                    <div class="col-span-2 text-sm text-slate-900">{{ $admin->username }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-500">Email</div>
                    <div class="col-span-2 text-sm text-slate-900">{{ $admin->email }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-500">Role</div>
                    <div class="col-span-2 text-sm text-slate-900">{{ $admin->role?->name ?? '-' }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-500">Status</div>
                    <div class="col-span-2 text-sm text-slate-900">{{ ucfirst($admin->status) }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-500">Phone</div>
                    <div class="col-span-2 text-sm text-slate-900">{{ $admin->phone ?: '-' }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-500">Last Login</div>
                    <div class="col-span-2 text-sm text-slate-900">
                        {{ $admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-500">Created At</div>
                    <div class="col-span-2 text-sm text-slate-900">
                        {{ $admin->created_at ? $admin->created_at->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Back
            </a>

            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" onsubmit="return confirm('Delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                    Delete
                </button>
            </form>
        </div>
    </div>
</x-layouts.dashboard>