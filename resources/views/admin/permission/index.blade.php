<x-layouts.admin :title="'Permission Management'">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Permission Management</h1>
                <p class="mt-1 text-sm text-gray-600">จัดการสิทธิ์การเข้าถึงของระบบ</p>
            </div>

            <a
                href="{{ route('admin.permissions.create') }}"
                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                เพิ่ม Permission
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.permissions.index') }}" class="grid gap-4 md:grid-cols-3">
                <div>
                    <label for="search" class="mb-1 block text-sm font-medium text-gray-700">ค้นหา</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="ค้นหาชื่อสิทธิ์ หรือ key"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                    >
                </div>

                <div>
                    <label for="group_key" class="mb-1 block text-sm font-medium text-gray-700">กลุ่มสิทธิ์</label>
                    <select
                        id="group_key"
                        name="group_key"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                    >
                        <option value="">ทั้งหมด</option>
                        @foreach ($groupOptions as $optionKey => $groupLabel)
                            <option value="{{ $optionKey }}" @selected($selectedGroupKey === $optionKey)>
                                {{ $groupLabel }} ({{ $optionKey }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.permissions.index') }}"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        @if ($groupedPermissions->isEmpty())
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm">
                ไม่พบข้อมูล permission
            </div>
        @else
            <div class="space-y-4">
                @foreach ($groupedPermissions as $group)
                    <details
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                        @if($selectedGroupKey === $group['group_key']) open @endif
                    >
                        <summary class="flex cursor-pointer items-center justify-between gap-4 px-4 py-4 marker:hidden">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                    {{ $group['group_key'] }}
                                </span>

                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $groupOptions[$group['group_key']] ?? ucfirst($group['group_key']) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $group['items']->count() }} permission
                                    </p>
                                </div>
                            </div>

                            <span class="text-xs text-gray-400">กดเพื่อซ่อน / แสดง</span>
                        </summary>

                        <div class="border-t border-gray-200">
                            <div class="divide-y divide-gray-200">
                                @foreach ($group['items'] as $permission)
                                    @php
                                        $parts = explode('.', $permission->key, 2);
                                        $actionKey = $parts[1] ?? '-';
                                    @endphp

                                    <div class="p-4">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="min-w-0 flex-1 space-y-3">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="text-sm font-semibold text-gray-900">
                                                        {{ $permission->name }}
                                                    </h3>

                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                                        {{ $actionKey }}
                                                    </span>
                                                </div>

                                                <div class="grid gap-3 md:grid-cols-2">
                                                    <div>
                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Key</p>
                                                        <p class="mt-1 break-all text-sm text-gray-800">
                                                            {{ $permission->key }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">คำอธิบาย</p>
                                                        <p class="mt-1 text-sm text-gray-700">
                                                            {{ $permission->description ?: '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex shrink-0 items-center gap-2">
                                                <a
                                                    href="{{ route('admin.permissions.edit', $permission) }}"
                                                    class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.permissions.destroy', $permission) }}"
                                                    onsubmit="return confirm('ยืนยันการลบ permission นี้หรือไม่?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center rounded-lg border border-red-300 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
            {{ $groupedPermissions->links() }}
        </div>
    </div>
</x-layouts.admin>