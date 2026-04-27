<x-layouts.admin title="จัดการบทบาท">
    <div class="space-y-6 text-white">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">จัดการบทบาท</h1>
                <p class="text-sm text-slate-400">จัดการบทบาทผู้ดูแลระบบ</p>
            </div>

            <a href="{{ route('admin.roles.create') }}"
               class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                สร้างบทบาท
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
            <form method="GET" action="{{ route('admin.roles.index') }}" class="flex gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="ค้นหาชื่อบทบาท"
                    class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-blue-400 focus:outline-none"
                >

                <button type="submit"
                        class="rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                    ค้นหา
                </button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead class="bg-white/[0.03] text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ชื่อ</th>
                        <th class="px-4 py-3 text-left font-semibold">รายละเอียด</th>
                        <th class="px-4 py-3 text-left font-semibold">ระบบ</th>
                        <th class="px-4 py-3 text-left font-semibold">จำนวนผู้ใช้</th>
                        <th class="px-4 py-3 text-right font-semibold">การจัดการ</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/10 text-slate-300">
                    @forelse ($roles as $role)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-4 py-3 text-white">{{ $role->name }}</td>
                            <td class="px-4 py-3">{{ $role->description ?: '-' }}</td>
                            <td class="px-4 py-3">
                                {{ $role->is_system ? 'ใช่' : 'ไม่ใช่' }}
                            </td>
                            <td class="px-4 py-3">{{ $role->admins_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="rounded-xl border border-white/10 px-3 py-1.5 text-xs text-slate-300 hover:bg-white/5">
                                        แก้ไข
                                    </a>

                                    <a href="{{ route('admin.roles.permissions.edit', $role) }}"
                                       class="rounded-xl border border-white/10 px-3 py-1.5 text-xs text-slate-300 hover:bg-white/5">
                                        สิทธิ์
                                    </a>

                                    @if (! $role->is_system)
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('ลบบทบาทนี้หรือไม่?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs text-red-300 hover:bg-red-500/10">
                                                ลบ
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                ไม่พบข้อมูลบทบาท
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="border-t border-white/10 px-4 py-3">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>