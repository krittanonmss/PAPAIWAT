<x-layouts.admin title="รายละเอียดรีวิว" header="รายละเอียดรีวิว">
    <div class="space-y-6 text-white">
        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm text-blue-300">รีวิววัด</p>
                    <h1 class="mt-1 text-2xl font-semibold">{{ $review->temple?->content?->title ?? '-' }}</h1>
                    <p class="mt-2 text-sm text-slate-400">{{ $review->created_at?->format('Y-m-d H:i') }} · {{ $review->rating }}/5 · {{ $review->status }}</p>
                </div>
                <a href="{{ route('admin.interactions.reviews.index') }}" class="rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-300 hover:bg-white/10">กลับ</a>
            </div>
            <p class="mt-6 rounded-2xl border border-white/10 bg-slate-950/40 p-5 text-slate-200">{{ $review->comment ?: '-' }}</p>
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 lg:col-span-2">
                <h2 class="text-base font-semibold">Reports</h2>
                <div class="mt-4 divide-y divide-white/10">
                    @forelse($review->reports as $report)
                        <div class="py-3 text-sm">
                            <p class="text-slate-200">{{ $report->reason ?: 'ไม่ระบุเหตุผล' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $report->created_at?->format('Y-m-d H:i') }} · visitor #{{ $report->anonymous_visitor_id ?: '-' }}</p>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-slate-400">ยังไม่มี report</p>
                    @endforelse
                </div>
            </section>

            <aside class="space-y-4">
                <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 text-sm">
                    <h2 class="text-base font-semibold">Visitor</h2>
                    <p class="mt-3 text-slate-300">ID: {{ $review->anonymous_visitor_id }}</p>
                    <p class="mt-1 text-slate-300">Status: {{ $review->visitor?->status ?? '-' }}</p>
                    <p class="mt-1 text-slate-500 break-all">IP hash: {{ $review->ip_hash ?: '-' }}</p>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5">
                    <h2 class="text-base font-semibold">Moderation</h2>
                    <p class="mt-3 text-sm text-slate-300">Reason: {{ $review->moderation_reason ?: '-' }}</p>
                    <p class="mt-1 text-sm text-slate-300">Note: {{ $review->moderation_note ?: '-' }}</p>
                    <div class="mt-4 grid gap-2">
                        @if($review->status !== 'approved')
                            <form method="POST" action="{{ route('admin.interactions.reviews.approve', $review) }}">@csrf @method('PATCH')<button class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">อนุมัติ</button></form>
                        @endif
                        @if(! in_array($review->status, ['rejected', 'spam'], true))
                            <form method="POST" action="{{ route('admin.interactions.reviews.reject', $review) }}">@csrf @method('PATCH')<input type="hidden" name="moderation_reason" value="off_topic"><button class="w-full rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white">ปฏิเสธ</button></form>
                            <form method="POST" action="{{ route('admin.interactions.reviews.spam', $review) }}">@csrf @method('PATCH')<button class="w-full rounded-xl border border-red-400/30 px-4 py-2 text-sm font-semibold text-red-200">สแปม</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.interactions.reviews.ban-visitor', $review) }}" onsubmit="return confirm('บล็อก visitor นี้หรือไม่?')">@csrf @method('PATCH')<input type="hidden" name="reason" value="moderation"><button class="w-full rounded-xl border border-amber-400/30 px-4 py-2 text-sm font-semibold text-amber-200">บล็อก visitor</button></form>
                        <form method="POST" action="{{ route('admin.interactions.reviews.destroy', $review) }}" onsubmit="return confirm('ลบรีวิวนี้หรือไม่?')">@csrf @method('DELETE')<input type="hidden" name="moderation_reason" value="other"><button class="w-full rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-300">ลบ</button></form>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-layouts.admin>
