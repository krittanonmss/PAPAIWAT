<x-layouts.admin title="Admin Dashboard" header="Dashboard">

    @if (session('success'))
        <div
            class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
            role="alert"
        >
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-base font-medium text-slate-900">เข้าสู่ระบบสำเร็จ</p>
        <p class="mt-2 text-sm text-slate-600">
            คุณกำลังใช้งานระบบจัดการหลังบ้านของ PAPAIWAT
        </p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- Login Logs --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Login Logs</h2>
                <p class="mt-1 text-sm text-slate-500">รายการเข้าสู่ระบบล่าสุด 10 รายการ</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">ID</th>
                            <th class="px-4 py-3 text-left font-semibold">Email</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Reason</th>
                            <th class="px-4 py-3 text-left font-semibold">IP</th>
                            <th class="px-4 py-3 text-left font-semibold">เวลา</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($loginLogs as $log)
                            <tr>
                                <td class="px-4 py-3">{{ $log->id }}</td>
                                <td class="px-4 py-3">{{ $log->email }}</td>
                                <td class="px-4 py-3">
                                    @if ($log->status === 'success')
                                        <span class="text-green-700 text-xs">success</span>
                                    @else
                                        <span class="text-red-700 text-xs">failed</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $log->reason ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $log->ip_address ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    {{ optional($log->created_at)->format('Y-m-d H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-slate-500">
                                    ยังไม่มีข้อมูล login log
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Activity Logs --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Activity Logs</h2>
                <p class="mt-1 text-sm text-slate-500">รายการ activity ล่าสุด 10 รายการ</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">ID</th>
                            <th class="px-4 py-3 text-left font-semibold">Admin ID</th>
                            <th class="px-4 py-3 text-left font-semibold">Action</th>
                            <th class="px-4 py-3 text-left font-semibold">Target</th>
                            <th class="px-4 py-3 text-left font-semibold">Method</th>
                            <th class="px-4 py-3 text-left font-semibold">IP</th>
                            <th class="px-4 py-3 text-left font-semibold">เวลา</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($activityLogs as $log)
                            <tr>
                                <td class="px-4 py-3">{{ $log->id }}</td>
                                <td class="px-4 py-3">{{ $log->admin_id }}</td>
                                <td class="px-4 py-3">{{ $log->action }}</td>
                                <td class="px-4 py-3">{{ $log->target }}</td>
                                <td class="px-4 py-3">{{ $log->method }}</td>
                                <td class="px-4 py-3">{{ $log->ip_address ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    {{ optional($log->created_at)->format('Y-m-d H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-slate-500">
                                    ยังไม่มีข้อมูล activity log
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</x-layouts.admin>