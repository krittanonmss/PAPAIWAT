<x-layouts.admin title="Admin Dashboard" header="Dashboard">

    @if (session('success'))
        <div
            class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
            role="alert"
        >
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">จำนวนผู้ดูแลระบบ</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">
                {{ number_format($adminCount) }}
            </p>
            <p class="mt-2 text-sm text-slate-600">
                จำนวน admin ทั้งหมดในระบบ
            </p>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">สถานะระบบ</p>
            <p class="mt-3 text-base font-semibold text-slate-900">
                พร้อมใช้งาน
            </p>
            <p class="mt-2 text-sm text-slate-600">
                แสดงข้อมูลการเข้าสู่ระบบล่าสุดของผู้ดูแลระบบ
            </p>
        </section>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">ประวัติการเข้าสู่ระบบ</h2>
            <p class="mt-1 text-sm text-slate-500">รายการ login ล่าสุด</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium">User</th>
                        <th class="px-3 py-2 text-left font-medium">Status</th>
                        <th class="px-3 py-2 text-left font-medium">IP</th>
                        <th class="px-3 py-2 text-left font-medium">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($loginLogs->take(5) as $log)
                        <tr>
                            <td class="px-3 py-2">
                                {{ $log->admin?->username ?? '-' }}
                            </td>

                            <td class="px-3 py-2">
                                @if ($log->status === 'success')
                                    <span class="text-green-600 text-xs">success</span>
                                @else
                                    <span class="text-red-600 text-xs">failed</span>
                                @endif
                            </td>

                            <td class="px-3 py-2">
                                {{ $log->ip_address ?: '-' }}
                            </td>

                            <td class="px-3 py-2">
                                {{ optional($log->created_at)->format('d/m H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-500">
                                ไม่มีข้อมูล
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</x-layouts.admin>