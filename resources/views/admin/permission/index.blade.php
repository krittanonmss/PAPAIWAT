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
                        @foreach ($groupOptions as $groupKey => $groupLabel)
                            <option value="{{ $groupKey }}" @selected($selectedGroupKey === $groupKey)>
                                {{ $groupLabel }} ({{ $groupKey }})
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

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">ชื่อสิทธิ์</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">กลุ่ม</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Key</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">คำอธิบาย</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($permissions as $permission)
                            @php
                                $parts = explode('.', $permission->key, 2);
                                $actionKey = $parts[1] ?? '-';
                            @endphp

                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $permission->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                        {{ $permission->group_key }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                        {{ $actionKey }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $permission->key }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $permission->description ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.permissions.edit', $permission) }}"
                                            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                        >
                                            แก้ไข
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
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">
                                    ไม่พบข้อมูล permission
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 px-4 py-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>