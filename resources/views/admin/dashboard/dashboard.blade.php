<x-layouts.admin title="Admin Dashboard" header="Dashboard">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div>
                <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                    Admin Dashboard
                </p>
                <h1 class="text-2xl font-bold text-white">แดชบอร์ดผู้ดูแลระบบ</h1>
                <p class="mt-1 text-sm text-slate-400">
                    ภาพรวมระบบผู้ดูแล และประวัติการเข้าสู่ระบบล่าสุด
                </p>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div
                class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300"
                role="alert"
            >
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
                <p class="text-xs font-medium text-slate-400">จำนวนผู้ดูแลระบบ</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-white">
                    {{ number_format($adminCount) }}
                </p>
                <p class="mt-1 text-sm text-slate-400">
                    จำนวน admin ทั้งหมดในระบบ
                </p>
            </section>

            <section class="rounded-3xl border border-blue-400/20 bg-blue-500/10 p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
                <p class="text-xs font-medium text-blue-300">สถานะระบบ</p>
                <p class="mt-2 inline-flex items-center gap-1.5 rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-300"></span>
                    พร้อมใช้งาน
                </p>
                <p class="mt-2 text-sm text-slate-400">
                    แสดงข้อมูลการเข้าสู่ระบบล่าสุดของผู้ดูแลระบบ
                </p>
            </section>
        </div>

        {{-- Login Logs --}}
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">ประวัติการเข้าสู่ระบบ</h2>
                    <p class="text-sm text-slate-400">รายการ login ล่าสุด</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ผู้ใช้งาน</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">IP Address</th>
                            <th class="px-4 py-3 text-left">เวลา</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($loginLogs->take(5) as $log)
                            <tr class="transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white shadow-lg shadow-indigo-950/30">
                                            {{ strtoupper(substr($log->admin?->username ?? '-', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">
                                                {{ $log->admin?->username ?? '-' }}
                                            </p>
                                            <p class="truncate text-xs text-slate-400">
                                                Admin Account
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($log->status === 'success')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                            สำเร็จ
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-400/20 bg-red-500/10 px-3 py-1 text-xs font-medium text-red-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-300"></span>
                                            ไม่สำเร็จ
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $log->ip_address ?: '-' }}
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ optional($log->created_at)->format('d/m/Y H:i') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ไม่มีข้อมูล</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        ยังไม่มีประวัติการเข้าสู่ระบบ
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</x-layouts.admin>