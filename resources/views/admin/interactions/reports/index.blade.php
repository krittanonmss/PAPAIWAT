<x-layouts.admin title="Reports" header="Reports">
    <div class="space-y-6 text-white">
        <section class="p-6">
            <p class="text-sm text-blue-300">การตรวจสอบ</p>
            <h1 class="mt-1 text-2xl font-bold">รายงานจากผู้ใช้</h1>
            <p class="mt-2 text-sm text-slate-400">ดูเหตุผลการ report และเชื่อมกลับไปยังรีวิวหรือความคิดเห็นต้นทาง</p>
        </section>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
        @endif

        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left">Target</th>
                        <th class="px-5 py-3 text-left">Reason</th>
                        <th class="px-5 py-3 text-left">Visitor</th>
                        <th class="px-5 py-3 text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($reports as $report)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-medium text-white">{{ class_basename($report->reportable_type) }} #{{ $report->reportable_id }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $report->created_at?->format('Y-m-d H:i') }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-300">{{ $report->reason ?: '-' }}</td>
                            <td class="px-5 py-4 text-slate-400">#{{ $report->anonymous_visitor_id ?: '-' }}</td>
                            <td class="px-5 py-4 text-right">
                                <form method="POST" action="{{ route('admin.interactions.reports.destroy', $report) }}" onsubmit="return confirm('ลบ report นี้หรือไม่?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-white/10">ลบ report</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">ยังไม่มี report</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        {{ $reports->links() }}
    </div>
</x-layouts.admin>
