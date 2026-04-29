<x-layouts.admin title="จัดการบทบาท">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">จัดการบทบาท</h1>
                <p class="text-sm text-slate-400">จัดการบทบาทผู้ดูแลระบบ</p>
            </div>

            <a href="{{ route('admin.roles.create') }}"
               class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                + สร้างบทบาท
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 shadow-xl shadow-slate-950/30 backdrop-blur">
            <form method="GET" action="{{ route('admin.roles.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-6">
                
                <div class="sm:col-span-4">
                    <label class="mb-1 block text-xs text-slate-400">ค้นหาบทบาท</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="เช่น Admin, Editor"
                        class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-blue-400 focus:outline-none"
                    >
                </div>

                <div class="sm:col-span-2 flex items-end gap-2">
                    <button type="submit"
                            class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        ค้นหา
                    </button>

                    <a href="{{ route('admin.roles.index') }}"
                       class="w-full text-center rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-300 hover:bg-white/5">
                        ล้าง
                    </a>
                </div>

            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">

            <div class="border-b border-white/10 px-4 py-3">
                <h2 class="text-sm font-semibold text-white">รายการบทบาท</h2>
                <p class="text-xs text-slate-400">บทบาททั้งหมดในระบบ พร้อมจำนวนผู้ใช้งาน</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/[0.03] text-xs uppercase tracking-wide text-slate-400">
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
                            <tr class="transition hover:bg-white/[0.05]">

                                {{-- Name --}}
                                <td class="px-4 py-3 font-semibold text-white">
                                    {{ $role->name }}
                                </td>

                                {{-- Description --}}
                                <td class="px-4 py-3 text-slate-400">
                                    {{ $role->description ?: '-' }}
                                </td>

                                {{-- System --}}
                                <td class="px-4 py-3">
                                    @if ($role->is_system)
                                        <span class="inline-flex rounded-full border border-yellow-400/20 bg-yellow-500/10 px-2.5 py-1 text-xs text-yellow-300">
                                            System
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-xs text-slate-400">
                                            Custom
                                        </span>
                                    @endif
                                </td>

                                {{-- Count --}}
                                <td class="px-4 py-3">
                                    {{ $role->admins_count }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('admin.roles.edit', $role) }}"
                                           class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 hover:bg-white/5">
                                            แก้ไข
                                        </a>

                                        <a href="{{ route('admin.roles.permissions.edit', $role) }}"
                                           class="rounded-xl bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700">
                                            สิทธิ์
                                        </a>

                                        @if (! $role->is_system)
                                            <form method="POST"
                                                  action="{{ route('admin.roles.destroy', $role) }}"
                                                  onsubmit="return confirm('ลบบทบาทนี้หรือไม่?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs font-medium text-red-300 hover:bg-red-500/10">
                                                    ลบ
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                    ไม่พบข้อมูลบทบาท
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-white/10 px-4 py-3">
                {{ $roles->links() }}
            </div>
        </div>

    </div>
</x-layouts.admin>