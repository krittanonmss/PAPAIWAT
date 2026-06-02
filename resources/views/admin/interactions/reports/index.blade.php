<x-layouts.admin title="Reports" header="Reports">
    @php
        $filterSelectClass = 'w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
        $filterKeys = ['search', 'type', 'visitor_id', 'date_from', 'date_to', 'per_page'];
        $activeFilterCount = collect(request()->only($filterKeys))
            ->reject(fn ($value, $key) => $key === 'per_page' && (string) $value === '20')
            ->filter(fn ($value) => filled($value))
            ->count();
    @endphp

    <div class="space-y-6 text-white">
        <section class="p-6">
            <p class="text-sm text-blue-300">การตรวจสอบ</p>
            <h1 class="mt-1 text-2xl font-bold">รายงานจากผู้ใช้</h1>
            <p class="mt-2 text-sm text-slate-400">ดูเหตุผลการ report และเชื่อมกลับไปยังรีวิวหรือความคิดเห็นต้นทาง</p>
        </section>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
        @endif

        <section class="relative z-40 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.interactions.reports.index') }}" class="space-y-4">
                <div class="flex flex-col gap-2 border-b border-white/10 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">ตัวกรองรายงาน</h2>
                        <p class="mt-0.5 text-xs text-slate-400">ค้นหาตามเหตุผล hash target ID ประเภทเนื้อหา ผู้เยี่ยมชม และช่วงวันที่ report</p>
                    </div>

                    @if ($activeFilterCount > 0)
                        <span class="inline-flex w-fit rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-200">
                            ใช้ตัวกรอง {{ $activeFilterCount }} รายการ
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                    <div class="lg:col-span-3">
                        <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหา</label>
                        <input id="search" type="text" name="search" value="{{ $filters['search'] ?? request('search') }}" placeholder="เหตุผล / hash / target ID" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="type" class="mb-1.5 block text-xs font-medium text-slate-400">ประเภท target</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'type',
                            'name' => 'type',
                            'selected' => $filters['type'] ?? request('type'),
                            'emptyLabel' => 'ทุกประเภท',
                            'placeholder' => 'เลือกประเภท',
                            'searchPlaceholder' => 'ค้นหาประเภท...',
                            'inputClass' => $filterSelectClass,
                            'options' => $types->map(fn ($type) => [
                                'value' => $type,
                                'label' => class_basename($type),
                                'search' => $type . ' ' . class_basename($type),
                            ]),
                        ])
                    </div>

                    <div class="lg:col-span-2">
                        <label for="visitor_id" class="mb-1.5 block text-xs font-medium text-slate-400">ผู้เยี่ยมชม</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'visitor_id',
                            'name' => 'visitor_id',
                            'selected' => $filters['visitor_id'] ?? request('visitor_id'),
                            'emptyLabel' => 'ทุกคน',
                            'placeholder' => 'เลือกผู้เยี่ยมชม',
                            'searchPlaceholder' => 'ค้นหา visitor...',
                            'inputClass' => $filterSelectClass,
                            'options' => $visitors->map(fn ($visitor) => [
                                'value' => (string) $visitor->id,
                                'label' => 'Visitor #' . $visitor->id,
                                'meta' => ($visitor->status ?? 'active') . ' · last seen ' . ($visitor->last_seen_at?->format('Y-m-d H:i') ?? '-'),
                                'search' => 'visitor ' . $visitor->id . ' ' . ($visitor->status ?? '') . ' ' . $visitor->visitor_uuid,
                            ]),
                        ])
                    </div>

                    <div class="lg:col-span-2">
                        <label for="date_from" class="mb-1.5 block text-xs font-medium text-slate-400">ตั้งแต่วันที่</label>
                        <input id="date_from" type="date" name="date_from" value="{{ $filters['date_from'] ?? request('date_from') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="date_to" class="mb-1.5 block text-xs font-medium text-slate-400">ถึงวันที่</label>
                        <input id="date_to" type="date" name="date_to" value="{{ $filters['date_to'] ?? request('date_to') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div class="lg:col-span-1">
                        <label for="per_page" class="mb-1.5 block text-xs font-medium text-slate-400">ต่อหน้า</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'per_page',
                            'name' => 'per_page',
                            'selected' => (string) ($filters['per_page'] ?? request('per_page', 20)),
                            'allowEmpty' => false,
                            'placeholder' => 'จำนวน',
                            'searchPlaceholder' => 'ค้นหา...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect(\App\Services\Admin\AdminPreferenceService::PER_PAGE_OPTIONS)->map(fn ($pageSize) => [
                                'value' => (string) $pageSize,
                                'label' => (string) $pageSize,
                                'search' => $pageSize . ' รายการ',
                            ]),
                        ])
                    </div>

                    <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                        <button type="submit" class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">ค้นหา</button>
                        <a href="{{ route('admin.interactions.reports.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5">ล้าง</a>
                    </div>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
            <div class="overflow-x-auto">
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
                            @php
                                $target = $report->reportable;
                                $isComment = $target instanceof \App\Models\Interaction\PublicComment;
                                $isReview = $target instanceof \App\Models\Interaction\TempleReview;
                                $source = $isComment ? $target?->commentable : ($isReview ? $target?->temple : null);
                                $sourceTitle = $source?->content?->title ?? '-';
                                $sourceType = $isComment
                                    ? ($target?->commentable_type === \App\Models\Content\Article\Article::class ? 'บทความ' : 'วัด')
                                    : ($isReview ? 'วัด' : class_basename($report->reportable_type));
                                $targetText = $isComment ? $target?->body : ($isReview ? $target?->comment : null);
                                $targetRoute = $isComment && $target
                                    ? route('admin.interactions.comments.show', $target)
                                    : ($isReview && $target ? route('admin.interactions.reviews.show', $target) : null);
                            @endphp
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="max-w-xl space-y-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full border border-blue-400/20 bg-blue-500/10 px-2.5 py-1 text-xs font-medium text-blue-200">
                                                {{ $isComment ? 'ความคิดเห็น' : ($isReview ? 'รีวิว' : class_basename($report->reportable_type)) }}
                                            </span>
                                            @if ($isReview)
                                                <span class="rounded-full border border-amber-400/20 bg-amber-500/10 px-2.5 py-1 text-xs font-medium text-amber-200">
                                                    {{ number_format($target->rating ?? 0) }} ดาว
                                                </span>
                                            @endif
                                            <span class="text-xs text-slate-500">#{{ $report->reportable_id }} · {{ $report->created_at?->format('Y-m-d H:i') }}</span>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-500">มาจาก{{ $sourceType }}</p>
                                            <p class="mt-0.5 font-medium text-white">{{ $sourceTitle }}</p>
                                        </div>

                                        <p class="rounded-2xl border border-white/10 bg-slate-950/30 px-3 py-2 text-sm leading-6 text-slate-300">
                                            {{ \Illuminate\Support\Str::limit($targetText ?: 'ไม่มีข้อความต้นทาง', 180) }}
                                        </p>

                                        @if ($targetRoute)
                                            <a href="{{ $targetRoute }}" class="inline-flex text-xs font-medium text-blue-300 transition hover:text-blue-200">
                                                เปิดรายการต้นทาง
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-300">{{ $report->reason ?: '-' }}</td>
                                <td class="px-5 py-4 text-slate-400">
                                    @if ($report->visitor)
                                        <div class="space-y-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-medium text-slate-200">Visitor #{{ $report->visitor->id }}</span>
                                                <span class="rounded-full border border-white/10 px-2 py-0.5 text-[11px] font-medium {{ $report->visitor->status === 'banned' ? 'bg-red-500/10 text-red-300' : 'bg-emerald-500/10 text-emerald-300' }}">
                                                    {{ $report->visitor->status ?? 'active' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-slate-500">ล่าสุด {{ $report->visitor->last_seen_at?->format('Y-m-d H:i') ?? '-' }}</p>
                                        </div>
                                    @else
                                        <span class="text-slate-500">ไม่ทราบผู้เยี่ยมชม</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.interactions.reports.destroy', $report) }}" onsubmit="return confirm('ลบ report นี้หรือไม่?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-white/10">ลบ report</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">ไม่พบ report ตามตัวกรอง</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $reports->links() }}
    </div>
</x-layouts.admin>
