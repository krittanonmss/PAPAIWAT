<x-layouts.admin title="Edit User">
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Edit User</h1>
                <p class="text-sm text-slate-600">Update administrator account.</p>
            </div>

            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                Back to Users
            </a>
        </div>

        {{-- Account Status --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-slate-900">Account Status</h2>
                <p class="text-sm text-slate-500">
                    Change user availability in the system.
                </p>
            </div>

            <form action="{{ route('admin.users.status.update', $admin) }}" method="POST">
                @csrf
                @method('PATCH')

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    >
                        <option value="active"
                            @selected(old('status', $admin->status) === 'active')>
                            Active
                        </option>

                        <option value="inactive"
                            @selected(old('status', $admin->status) === 'inactive')>
                            Inactive
                        </option>
                    </select>

                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-black px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        Update Status
                    </button>
                </div>
            </form>
        </div>
            <div class="space-y-6 lg:col-span-2">
                <form method="POST" action="{{ route('admin.users.update', $admin) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-base font-semibold text-slate-900">Account Information</h2>
                        <p class="mt-1 text-sm text-slate-600">Update the basic profile details for this administrator.</p>

                        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="username" class="mb-1 block text-sm font-medium text-slate-700">Username</label>
                                <input
                                    type="text"
                                    name="username"
                                    id="username"
                                    value="{{ old('username', $admin->username) }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                >
                                @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="{{ old('email', $admin->email) }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                >
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="role_id" class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                                <select
                                    name="role_id"
                                    id="role_id"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                >
                                    <option value="">Select role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role_id', $admin->role_id) == $role->id)>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
                                <input
                                    type="text"
                                    name="phone"
                                    id="phone"
                                    value="{{ old('phone', $admin->phone) }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                >
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-base font-semibold text-slate-900">Change Password</h2>
                        <p class="mt-1 text-sm text-slate-600">Leave these fields empty if you do not want to change the password.</p>

                        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="password" class="mb-1 block text-sm font-medium text-slate-700">New Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                >
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm New Password</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                        >
                            Update
                        </button>

                        <a
                            href="{{ route('admin.users.index') }}"
                            class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>