<x-layouts.admin title="รายละเอียดผู้ใช้งาน">
    <div class="mx-auto max-w-3xl space-y-6 text-white">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">รายละเอียดผู้ใช้งาน</h1>
                <p class="text-sm text-slate-400">ข้อมูลบัญชีผู้ดูแลระบบ</p>
            </div>

            <a href="{{ route('admin.users.edit', $admin) }}"
               class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                แก้ไข
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="divide-y divide-white/10">

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-400">ชื่อผู้ใช้</div>
                    <div class="col-span-2 text-sm text-white">{{ $admin->username }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-400">อีเมล</div>
                    <div class="col-span-2 text-sm text-white">{{ $admin->email }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-400">บทบาท</div>
                    <div class="col-span-2 text-sm text-white">{{ $admin->role?->name ?? '-' }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-400">สถานะ</div>
                    <div class="col-span-2">
                        @if ($admin->status === 'active')
                            <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs text-emerald-300 border border-emerald-400/20">
                                ● ใช้งาน
                            </span>
                        @else
                            <span class="rounded-full bg-red-500/10 px-3 py-1 text-xs text-red-300 border border-red-400/20">
                                ● ไม่ใช้งาน
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-400">เบอร์โทร</div>
                    <div class="col-span-2 text-sm text-white">{{ $admin->phone ?: '-' }}</div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-400">เข้าสู่ระบบล่าสุด</div>
                    <div class="col-span-2 text-sm text-slate-300">
                        {{ $admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>

                <div class="grid grid-cols-3 px-6 py-4">
                    <div class="text-sm font-medium text-slate-400">สร้างเมื่อ</div>
                    <div class="col-span-2 text-sm text-slate-300">
                        {{ $admin->created_at ? $admin->created_at->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-white/5">
                กลับ
            </a>

            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" onsubmit="return confirm('ลบผู้ใช้นี้หรือไม่?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center rounded-xl border border-red-400/20 px-4 py-2 text-sm font-medium text-red-300 hover:bg-red-500/10">
                    ลบ
                </button>
            </form>
        </div>

    </div>
</x-layouts.admin>