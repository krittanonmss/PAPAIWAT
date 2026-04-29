<x-layouts.admin title="รายละเอียดผู้ใช้งาน">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">รายละเอียดผู้ใช้งาน</h1>
                <p class="text-sm text-slate-400">ข้อมูลบัญชีผู้ดูแลระบบ</p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-white/5"
                >
                    กลับ
                </a>

                <a
                    href="{{ route('admin.users.edit', $admin) }}"
                    class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    แก้ไข
                </a>
            </div>
        </div>

        {{-- Profile Summary --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 text-lg font-bold text-white shadow-lg shadow-blue-950/30">
                        {{ strtoupper(substr($admin->username, 0, 1)) }}
                    </div>

                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-bold text-white">{{ $admin->username }}</h2>
                        <p class="truncate text-sm text-slate-400">{{ $admin->email }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-slate-300">
                        {{ $admin->role?->name ?? '-' }}
                    </span>

                    @if ($admin->status === 'active')
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1.5 text-xs font-medium text-emerald-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                            ใช้งาน
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-400/20 bg-red-500/10 px-3 py-1.5 text-xs font-medium text-red-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-300"></span>
                            ไม่ใช้งาน
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Detail Cards --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

            {{-- Account Information --}}
            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                <h2 class="text-base font-semibold text-white">ข้อมูลบัญชี</h2>
                <p class="mt-1 text-sm text-slate-400">ข้อมูลพื้นฐานของผู้ดูแลระบบ</p>

                <div class="mt-5 divide-y divide-white/10">
                    <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                        <div class="text-sm font-medium text-slate-400">ชื่อผู้ใช้</div>
                        <div class="text-sm text-white sm:col-span-2">{{ $admin->username }}</div>
                    </div>

                    <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                        <div class="text-sm font-medium text-slate-400">อีเมล</div>
                        <div class="break-all text-sm text-white sm:col-span-2">{{ $admin->email }}</div>
                    </div>

                    <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                        <div class="text-sm font-medium text-slate-400">บทบาท</div>
                        <div class="text-sm text-white sm:col-span-2">{{ $admin->role?->name ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                        <div class="text-sm font-medium text-slate-400">เบอร์โทร</div>
                        <div class="text-sm text-white sm:col-span-2">{{ $admin->phone ?: '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Usage Information --}}
            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                <h2 class="text-base font-semibold text-white">ข้อมูลการใช้งาน</h2>
                <p class="mt-1 text-sm text-slate-400">สถานะและประวัติการเข้าใช้งาน</p>

                <div class="mt-5 divide-y divide-white/10">
                    <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                        <div class="text-sm font-medium text-slate-400">สถานะ</div>
                        <div class="sm:col-span-2">
                            @if ($admin->status === 'active')
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1.5 text-xs font-medium text-emerald-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                    ใช้งาน
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-red-400/20 bg-red-500/10 px-3 py-1.5 text-xs font-medium text-red-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-300"></span>
                                    ไม่ใช้งาน
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                        <div class="text-sm font-medium text-slate-400">เข้าสู่ระบบล่าสุด</div>
                        <div class="text-sm text-slate-300 sm:col-span-2">
                            {{ $admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                        <div class="text-sm font-medium text-slate-400">สร้างเมื่อ</div>
                        <div class="text-sm text-slate-300 sm:col-span-2">
                            {{ $admin->created_at ? $admin->created_at->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center rounded-xl border border-white/10 px-5 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/5"
            >
                กลับ
            </a>

            <a
                href="{{ route('admin.users.edit', $admin) }}"
                class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
            >
                แก้ไข
            </a>

            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" onsubmit="return confirm('ลบผู้ใช้นี้หรือไม่?');">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center rounded-xl border border-red-400/20 px-5 py-2.5 text-sm font-medium text-red-300 hover:bg-red-500/10"
                >
                    ลบ
                </button>
            </form>
        </div>

    </div>
</x-layouts.admin>