<x-layouts.admin title="Admin Dashboard" header="Dashboard">

    <div class="min-h-screen rounded-2xl bg-slate-950/95 p-4 text-white sm:p-6">
        @if (session('success'))
            <div
                class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300 shadow-lg shadow-emerald-950/20 backdrop-blur"
                role="alert"
            >
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
            <section class="rounded-2xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                <p class="text-sm font-medium text-slate-400">จำนวนผู้ดูแลระบบ</p>
                <p class="mt-3 text-4xl font-bold tracking-tight text-white">
                    {{ number_format($adminCount) }}
                </p>
                <p class="mt-2 text-sm text-slate-400">
                    จำนวน admin ทั้งหมดในระบบ
                </p>
            </section>

            <section class="rounded-2xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                <p class="text-sm font-medium text-slate-400">สถานะระบบ</p>
                <p class="mt-3 inline-flex rounded-full border border-blue-400/20 bg-blue-900/70 px-3 py-1 text-sm font-semibold text-blue-300">
                    พร้อมใช้งาน
                </p>
                <p class="mt-3 text-sm text-slate-400">
                    แสดงข้อมูลการเข้าสู่ระบบล่าสุดของผู้ดูแลระบบ
                </p>
            </section>
        </div>

        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-5">
                <h2 class="text-lg font-semibold text-white">ประวัติการเข้าสู่ระบบ</h2>
                <p class="mt-1 text-sm text-slate-400">รายการ login ล่าสุด</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-xs">
                    <thead class="bg-white/[0.03] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">ผู้ใช้งาน</th>
                            <th class="px-4 py-3 text-left font-medium">สถานะ</th>
                            <th class="px-4 py-3 text-left font-medium">IP Address</th>
                            <th class="px-4 py-3 text-left font-medium">เวลา</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($loginLogs->take(5) as $log)
                            <tr class="transition hover:bg-white/5">
                                <td class="px-4 py-3">
                                    {{ $log->admin?->username ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    @if ($log->status === 'success')
                                        <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-300">
                                            สำเร็จ
                                        </span>
                                    @else
                                        <span class="rounded-full border border-red-400/20 bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-300">
                                            ไม่สำเร็จ
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $log->ip_address ?: '-' }}
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ optional($log->created_at)->format('d/m H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">
                                    ไม่มีข้อมูล
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

</x-layouts.admin>