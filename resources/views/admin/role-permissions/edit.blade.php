<x-layouts.admin title="Role Permissions">
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Role Permissions</h1>
            <p class="text-sm text-slate-600">Manage permissions for role: {{ $role->name }}</p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}" class="space-y-6">
                @csrf
                @method('PUT')

                @foreach ($permissions as $groupKey => $groupPermissions)
                    <div class="space-y-3">
                        <h2 class="text-lg font-semibold text-slate-900">
                            {{ ucfirst($groupKey) }}
                        </h2>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @foreach ($groupPermissions as $permission)
                                <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-4">
                                    <input
                                        type="checkbox"
                                        name="permission_ids[]"
                                        value="{{ $permission->id }}"
                                        class="mt-1 rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                                        {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                                    >

                                    <div>
                                        <div class="text-sm font-medium text-slate-900">
                                            {{ $permission->name }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ $permission->key }}
                                        </div>
                                        @if ($permission->description)
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $permission->description }}
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Save Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>