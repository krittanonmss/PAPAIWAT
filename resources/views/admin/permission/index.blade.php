<x-layouts.admin :title="'จัดการ Permission'">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Access Management
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการ Permission</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการสิทธิ์การเข้าถึงของระบบ แยกตามกลุ่มการใช้งาน
                    </p>
                </div>

                <a
                    href="{{ route('admin.permissions.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    เพิ่ม Permission
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.permissions.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหา Permission</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="ค้นหาชื่อสิทธิ์ หรือ key"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-4">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">กลุ่มสิทธิ์</label>
                    <select
                        name="group_key"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option class="bg-slate-900 text-white" value="">ทั้งหมด</option>
                        @foreach ($groupOptions as $optionKey => $groupLabel)
                            <option class="bg-slate-900 text-white" value="{{ $optionKey }}" @selected($selectedGroupKey === $optionKey)>
                                {{ $groupLabel }} ({{ $optionKey }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2 lg:col-span-3 lg:self-end">
                    <button
                        type="submit"
                        class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.permissions.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                    >
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        {{-- Permission Groups --}}
        @if ($groupedPermissions->isEmpty())
            <div class="rounded-3xl border border-white/10 bg-white/[0.04] px-5 py-10 text-center shadow-lg shadow-slate-950/20 backdrop-blur">
                <p class="text-base font-medium text-slate-300">ไม่พบข้อมูล Permission</p>
                <p class="mt-1 text-sm text-slate-500">ลองเปลี่ยนเงื่อนไขการค้นหา หรือเพิ่ม Permission ใหม่</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($groupedPermissions as $group)
                    <details
                        class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur"
                        @if($selectedGroupKey === $group['group_key']) open @endif
                    >
                        <summary class="cursor-pointer list-none border-b border-white/10 px-5 py-4 transition hover:bg-white/[0.06]">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white shadow-lg shadow-indigo-950/30">
                                        {{ strtoupper(substr($group['group_key'], 0, 1)) }}
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h2 class="text-base font-semibold text-white">
                                                {{ $groupOptions[$group['group_key']] ?? ucfirst($group['group_key']) }}
                                            </h2>

                                            <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                                {{ $group['group_key'] }}
                                            </span>
                                        </div>

                                        <p class="mt-1 text-sm text-slate-400">
                                            {{ $group['items']->count() }} permission
                                        </p>
                                    </div>
                                </div>

                                <span class="text-xs font-medium text-slate-500 transition group-open:text-blue-300">
                                    กดเพื่อซ่อน / แสดง
                                </span>
                            </div>
                        </summary>

                        <div class="divide-y divide-white/10">
                            @foreach ($group['items'] as $permission)
                                @php
                                    $parts = explode('.', $permission->key, 2);
                                    $actionKey = $parts[1] ?? '-';
                                @endphp

                                <div class="px-5 py-4 transition hover:bg-white/[0.06]">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="min-w-0 flex-1 space-y-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="text-sm font-semibold text-white">
                                                    {{ $permission->name }}
                                                </h3>

                                                <span class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                                                    {{ $actionKey }}
                                                </span>
                                            </div>

                                            <div class="grid gap-3 md:grid-cols-2">
                                                <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-3">
                                                    <p class="text-xs uppercase tracking-wide text-slate-500">Key</p>
                                                    <p class="mt-1 break-all text-sm text-slate-300">
                                                        {{ $permission->key }}
                                                    </p>
                                                </div>

                                                <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-3">
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
                                                class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
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
                                                    class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs font-medium text-red-300 transition hover:bg-red-500/10"
                                                >
                                                    ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endforeach
            </div>
        @endif

        <div class="rounded-3xl border border-white/10 bg-white/[0.04] px-5 py-3 shadow-lg shadow-slate-950/20 backdrop-blur">
            {{ $groupedPermissions->links() }}
        </div>

    </div>
</x-layouts.admin>