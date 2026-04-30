<x-layouts.admin title="จัดการบทบาท">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Access Management
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการบทบาท</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการบทบาทผู้ดูแลระบบ และกำหนดสิทธิ์การเข้าถึงแต่ละส่วน
                    </p>
                </div>

                <a
                    href="{{ route('admin.roles.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    สร้างบทบาท
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
            <form method="GET" action="{{ route('admin.roles.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-9">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาบทบาท</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="เช่น Admin, Editor"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="grid grid-cols-2 gap-2 lg:col-span-3 lg:self-end">
                    <button
                        type="submit"
                        class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.roles.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                    >
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการบทบาท</h2>
                    <p class="text-sm text-slate-400">บทบาททั้งหมดในระบบ พร้อมจำนวนผู้ใช้งาน</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ชื่อ</th>
                            <th class="px-4 py-3 text-left">รายละเอียด</th>
                            <th class="px-4 py-3 text-left">ประเภท</th>
                            <th class="px-4 py-3 text-left">ผู้ใช้</th>
                            <th class="px-4 py-3 text-right">การจัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($roles as $role)
                            <tr class="transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white shadow-lg shadow-indigo-950/30">
                                            {{ strtoupper(substr($role->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">{{ $role->name }}</p>
                                            <p class="truncate text-xs text-slate-400">
                                                {{ $role->is_system ? 'บทบาทหลักของระบบ' : 'บทบาทที่กำหนดเอง' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $role->description ?: '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    @if ($role->is_system)
                                        <span class="inline-flex rounded-full border border-yellow-400/20 bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-300">
                                            System
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                            Custom
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                                        {{ $role->admins_count }} ผู้ใช้
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.roles.edit', $role) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            แก้ไข
                                        </a>

                                        <a
                                            href="{{ route('admin.roles.permissions.edit', $role) }}"
                                            class="rounded-xl border border-blue-400/20 bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                                        >
                                            สิทธิ์
                                        </a>

                                        @if (! $role->is_system)
                                            <form
                                                method="POST"
                                                action="{{ route('admin.roles.destroy', $role) }}"
                                                onsubmit="return confirm('ลบบทบาทนี้หรือไม่?');"
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
                                        @else
                                            <button
                                                type="button"
                                                disabled
                                                class="cursor-not-allowed rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-600"
                                            >
                                                ลบ
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ไม่พบข้อมูลบทบาท</p>
                                    <p class="mt-1 text-sm text-slate-500">ลองเปลี่ยนเงื่อนไขการค้นหา หรือสร้างบทบาทใหม่</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-white/10 px-5 py-3">
                {{ $roles->links() }}
            </div>
        </div>

    </div>
</x-layouts.admin>