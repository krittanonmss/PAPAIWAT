<x-layouts.admin title="Bans" header="Bans">
    <div class="space-y-6 text-white">
        <section class="p-6">
            <p class="text-sm text-blue-300">การตรวจสอบ</p>
            <h1 class="mt-1 text-2xl font-bold">รายการบล็อก</h1>
            <p class="mt-2 text-sm text-slate-400">จัดการ visitor ที่ถูก block จากระบบ interaction</p>
        </section>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
        @endif

        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
            <table class="min-w-full divide-y divide-white/10 text-sm">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-left">Hash</th>
                        <th class="px-5 py-3 text-left">Reason</th>
                        <th class="px-5 py-3 text-left">Expires</th>
                        <th class="px-5 py-3 text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($bans as $ban)
                        <tr>
                            <td class="px-5 py-4 text-slate-300">{{ $ban->ban_type }}</td>
                            <td class="px-5 py-4 text-xs text-slate-500">{{ $ban->value_hash }}</td>
                            <td class="px-5 py-4 text-slate-300">{{ $ban->reason ?: '-' }}</td>
                            <td class="px-5 py-4 text-slate-400">{{ $ban->expires_at?->format('Y-m-d H:i') ?? 'ไม่มีวันหมดอายุ' }}</td>
                            <td class="px-5 py-4 text-right">
                                <form method="POST" action="{{ route('admin.interactions.bans.destroy', $ban) }}" onsubmit="return confirm('ปลดบล็อกหรือไม่?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-white/10">ปลดบล็อก</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">ยังไม่มีรายการบล็อก</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        {{ $bans->links() }}
    </div>
</x-layouts.admin>
