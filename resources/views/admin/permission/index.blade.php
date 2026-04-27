<x-layouts.admin :title="'จัดการ Permission'">
    <div class="space-y-6 text-white">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Permission Management</h1>
                <p class="mt-1 text-sm text-slate-400">จัดการสิทธิ์การเข้าถึงของระบบ</p>
            </div>

            <a
                href="{{ route('admin.permissions.create') }}"
                class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:opacity-90"
            >
                เพิ่ม Permission
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300 backdrop-blur">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-300 backdrop-blur">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 shadow-xl shadow-slate-950/30 backdrop-blur">
            <form method="GET" action="{{ route('admin.permissions.index') }}" class="grid gap-4 md:grid-cols-3">

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-400">ค้นหา</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="ค้นหาชื่อสิทธิ์ หรือ key"
                        class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-blue-400 focus:outline-none"
                    >
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-400">กลุ่มสิทธิ์</label>
                    <select
                        name="group_key"
                        class="w-full appearance-none rounded-xl border border-white/10 bg-slate-900 px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                    >
                        <option class="bg-slate-900 text-white" value="">ทั้งหมด</option>
                        @foreach ($groupOptions as $optionKey => $groupLabel)
                            <option class="bg-slate-900 text-white" value="{{ $optionKey }}" @selected($selectedGroupKey === $optionKey)>
                                {{ $groupLabel }} ({{ $optionKey }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:opacity-90"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.permissions.index') }}"
                        class="inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-white/5"
                    >
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        @if ($groupedPermissions->isEmpty())
            <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-6 text-center text-sm text-slate-400 shadow-xl backdrop-blur">
                ไม่พบข้อมูล permission
            </div>
        @else
            <div class="space-y-4">
                @foreach ($groupedPermissions as $group)
                    <details
                        class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl backdrop-blur"
                        @if($selectedGroupKey === $group['group_key']) open @endif
                    >
                        <summary class="flex cursor-pointer items-center justify-between gap-4 px-4 py-4 marker:hidden hover:bg-white/5">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-slate-300">
                                    {{ $group['group_key'] }}
                                </span>

                                <div>
                                    <p class="text-sm font-semibold text-white">
                                        {{ $groupOptions[$group['group_key']] ?? ucfirst($group['group_key']) }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $group['items']->count() }} permission
                                    </p>
                                </div>
                            </div>

                            <span class="text-xs text-slate-500">กดเพื่อซ่อน / แสดง</span>
                        </summary>

                        <div class="border-t border-white/10">
                            <div class="divide-y divide-white/10">
                                @foreach ($group['items'] as $permission)
                                    @php
                                        $parts = explode('.', $permission->key, 2);
                                        $actionKey = $parts[1] ?? '-';
                                    @endphp

                                    <div class="p-4 hover:bg-white/5 transition">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

                                            <div class="min-w-0 flex-1 space-y-3">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="text-sm font-semibold text-white">
                                                        {{ $permission->name }}
                                                    </h3>

                                                    <span class="inline-flex items-center rounded-full bg-white/10 px-2.5 py-1 text-xs font-medium text-slate-300">
                                                        {{ $actionKey }}
                                                    </span>
                                                </div>

                                                <div class="grid gap-3 md:grid-cols-2">
                                                    <div>
                                                        <p class="text-xs uppercase tracking-wide text-slate-500">Key</p>
                                                        <p class="mt-1 break-all text-sm text-slate-300">
                                                            {{ $permission->key }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <p class="text-xs uppercase tracking-wide text-slate-500">คำอธิบาย</p>
                                                        <p class="mt-1 text-sm text-slate-300">
                                                            {{ $permission->description ?: '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex shrink-0 items-center gap-2">
                                                <a
                                                    href="{{ route('admin.permissions.edit', $permission) }}"
                                                    class="inline-flex items-center rounded-xl border border-white/10 px-3 py-2 text-sm text-slate-300 hover:bg-white/5"
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
                                                        class="inline-flex items-center rounded-xl border border-red-400/20 px-3 py-2 text-sm text-red-300 hover:bg-red-500/10"
                                                    >
                                                        ลบ
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

        <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 shadow-xl backdrop-blur">
            {{ $groupedPermissions->links() }}
        </div>

    </div>
</x-layouts.admin>