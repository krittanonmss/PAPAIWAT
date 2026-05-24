<x-layouts.admin title="Review การตรวจสอบ" header="รีวิว">
    @php
        $statusStyles = [
            'pending' => 'border-yellow-400/20 bg-yellow-500/10 text-yellow-300',
            'approved' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300',
            'rejected' => 'border-red-400/20 bg-red-500/10 text-red-300',
            'spam' => 'border-red-400/20 bg-red-500/10 text-red-300',
        ];
        $filterSelectClass = 'w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
        $filterKeys = ['search', 'status', 'rating', 'reported', 'queue', 'per_page'];
        $activeFilterCount = collect(request()->only($filterKeys))
            ->reject(fn ($value, $key) => $key === 'per_page' && (string) $value === '20')
            ->filter(fn ($value) => filled($value))
            ->count();
    @endphp

    <div class="space-y-6 text-white">
        <section class="p-6">
            <div>
                <div>
                    <p class="text-sm font-medium text-blue-300">การตรวจสอบ</p>
                    <h1 class="mt-1 text-2xl font-bold">รีวิว</h1>
                    <p class="mt-2 text-sm text-slate-400">ตรวจสอบ อนุมัติ หรือปฏิเสธรีวิวจากผู้ใช้ public</p>
                </div>
            </div>
        </section>

        <section class="grid gap-3 md:grid-cols-5">
            @foreach([
                ['pending', 'รอตรวจ', $summary['pending'] ?? 0],
                ['reported', 'ถูกรายงาน', $summary['reported'] ?? 0],
                ['auto_hidden', 'ซ่อนอัตโนมัติ', $summary['auto_hidden'] ?? 0],
                ['approved_today', 'อนุมัติวันนี้', $summary['approved_today'] ?? 0],
                ['rejected_today', 'ปฏิเสธวันนี้', $summary['rejected_today'] ?? 0],
            ] as [$queue, $label, $count])
                <a
                    href="{{ in_array($queue, ['approved_today', 'rejected_today'], true) ? route('admin.interactions.reviews.index') : route('admin.interactions.reviews.index', ['queue' => $queue]) }}"
                    class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 transition hover:border-blue-400/30 hover:bg-white/[0.07]"
                >
                    <p class="text-xs text-slate-400">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($count) }}</p>
                </a>
            @endforeach
        </section>

        <section class="relative z-40 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.interactions.reviews.index') }}" class="space-y-4">
                <div class="flex flex-col gap-2 border-b border-white/10 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">ตัวกรองรีวิว</h2>
                        <p class="mt-0.5 text-xs text-slate-400">ค้นหาตามชื่อวัด slug ความคิดเห็น ผู้แสดงชื่อ สถานะ คะแนน และจำนวนรายงาน</p>
                    </div>

                    @if ($activeFilterCount > 0)
                        <span class="inline-flex w-fit rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-200">
                            ใช้ตัวกรอง {{ $activeFilterCount }} รายการ
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                    <div class="lg:col-span-4">
                        <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหา</label>
                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $filters['search'] ?? request('search') }}"
                            placeholder="ชื่อวัด / slug / รีวิว / ผู้แสดงชื่อ"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div class="lg:col-span-2">
                        <label for="status" class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'status',
                            'name' => 'status',
                            'selected' => $filters['status'] ?? request('status'),
                            'emptyLabel' => 'ทุกสถานะ',
                            'placeholder' => 'เลือกสถานะ',
                            'searchPlaceholder' => 'ค้นหาสถานะ...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect([
                                ['value' => 'pending', 'label' => 'รอตรวจ', 'search' => 'pending รอตรวจ'],
                                ['value' => 'approved', 'label' => 'อนุมัติ', 'search' => 'approved อนุมัติ'],
                                ['value' => 'rejected', 'label' => 'ปฏิเสธ', 'search' => 'rejected ปฏิเสธ'],
                                ['value' => 'spam', 'label' => 'สแปม', 'search' => 'spam สแปม'],
                            ]),
                        ])
                    </div>

                    <div class="lg:col-span-2">
                        <label for="rating" class="mb-1.5 block text-xs font-medium text-slate-400">คะแนน</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'rating',
                            'name' => 'rating',
                            'selected' => $filters['rating'] ?? request('rating'),
                            'emptyLabel' => 'ทุกคะแนน',
                            'placeholder' => 'เลือกคะแนน',
                            'searchPlaceholder' => 'ค้นหาคะแนน...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect([5, 4, 3, 2, 1])->map(fn ($rating) => [
                                'value' => (string) $rating,
                                'label' => $rating . ' ดาว',
                                'search' => $rating . ' ดาว',
                            ]),
                        ])
                    </div>

	                    <div class="lg:col-span-2">
	                        <label for="reported" class="mb-1.5 block text-xs font-medium text-slate-400">รายงาน</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'reported',
                            'name' => 'reported',
                            'selected' => $filters['reported'] ?? request('reported'),
                            'emptyLabel' => 'ทั้งหมด',
                            'placeholder' => 'เลือกสถานะรายงาน',
                            'searchPlaceholder' => 'ค้นหารายงาน...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect([
                                ['value' => 'yes', 'label' => 'มีรายงาน', 'search' => 'yes มีรายงาน'],
                                ['value' => 'no', 'label' => 'ไม่มีรายงาน', 'search' => 'no ไม่มีรายงาน'],
                            ]),
                        ])
	                    </div>

	                    <div class="lg:col-span-2">
	                        <label for="queue" class="mb-1.5 block text-xs font-medium text-slate-400">คิวงาน</label>
	                        @include('admin.content.partials._searchable_select', [
	                            'id' => 'queue',
	                            'name' => 'queue',
	                            'selected' => $filters['queue'] ?? request('queue'),
	                            'emptyLabel' => 'ทุกคิว',
	                            'placeholder' => 'เลือกคิวงาน',
	                            'searchPlaceholder' => 'ค้นหาคิว...',
	                            'inputClass' => $filterSelectClass,
	                            'options' => collect([
	                                ['value' => 'pending', 'label' => 'รอตรวจ', 'search' => 'pending รอตรวจ'],
	                                ['value' => 'reported', 'label' => 'ถูกรายงาน', 'search' => 'reported รายงาน'],
	                                ['value' => 'auto_hidden', 'label' => 'ซ่อนอัตโนมัติ', 'search' => 'auto hidden ซ่อนอัตโนมัติ'],
	                            ]),
	                        ])
	                    </div>

                    <div class="lg:col-span-2">
                        <label for="per_page" class="mb-1.5 block text-xs font-medium text-slate-400">แสดงต่อหน้า</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'per_page',
                            'name' => 'per_page',
                            'selected' => (string) ($filters['per_page'] ?? request('per_page', 20)),
                            'allowEmpty' => false,
                            'placeholder' => 'เลือกจำนวนต่อหน้า',
                            'searchPlaceholder' => 'ค้นหาจำนวน...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect(\App\Services\Admin\AdminPreferenceService::PER_PAGE_OPTIONS)->map(fn ($pageSize) => [
                                'value' => (string) $pageSize,
                                'label' => $pageSize . ' รายการ',
                                'search' => $pageSize . ' รายการ',
                            ]),
                        ])
                    </div>

                    <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                        <button type="submit" class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">ค้นหา</button>
                        <a href="{{ route('admin.interactions.reviews.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5">ล้าง</a>
                    </div>
                </div>
            </form>
        </section>

	        @if (session('success'))
	            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
	        @endif

	        <form id="bulk-reviews-form" method="POST" action="{{ route('admin.interactions.reviews.bulk') }}" class="rounded-3xl border border-white/10 bg-white/[0.04] p-4">
	            @csrf
	            @method('PATCH')
	            <div class="grid gap-3 lg:grid-cols-12">
	                <div class="lg:col-span-2">
	                    <select name="action" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white">
	                        <option value="approve">อนุมัติ</option>
	                        <option value="reject">ปฏิเสธ</option>
	                        <option value="spam">สแปม</option>
	                        <option value="delete">ลบ</option>
	                    </select>
	                </div>
	                <div class="lg:col-span-2">
	                    <select name="moderation_reason" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white">
	                        <option value="">ไม่ระบุเหตุผล</option>
	                        <option value="spam">Spam</option>
	                        <option value="offensive">Offensive</option>
	                        <option value="duplicate">Duplicate</option>
	                        <option value="off_topic">Off-topic</option>
	                        <option value="other">Other</option>
	                    </select>
	                </div>
	                <input name="moderation_note" class="lg:col-span-6 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white" placeholder="Moderation note">
	                <button class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white lg:col-span-2">ใช้กับรายการที่เลือก</button>
	            </div>
	        </form>

	        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead>
	                        <tr>
	                            <th class="px-5 py-3 text-left font-medium">เลือก</th>
	                            <th class="px-5 py-3 text-left font-medium">วัด</th>
                            <th class="px-5 py-3 text-left font-medium">รีวิว</th>
                            <th class="px-5 py-3 text-left font-medium">สถานะ</th>
                            <th class="px-5 py-3 text-right font-medium">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($reviews as $review)
	                            <tr>
	                                <td class="px-5 py-4 align-top">
	                                    <input form="bulk-reviews-form" type="checkbox" name="review_ids[]" value="{{ $review->id }}" class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600">
	                                </td>
	                                <td class="px-5 py-4 align-top">
	                                    <a href="{{ $review->temple ? route('admin.temples.show', $review->temple) : '#' }}" class="font-semibold text-white hover:text-blue-300">{{ $review->temple?->content?->title ?? '-' }}</a>
	                                    <p class="mt-1 text-xs text-slate-500">{{ $review->created_at?->format('Y-m-d H:i') }}</p>
	                                </td>
                                <td class="px-5 py-4 align-top">
                                    <p class="font-medium text-amber-300">{{ number_format($review->rating) }} / 5</p>
                                    <p class="mt-1 text-slate-300">{{ $review->comment ?: '-' }}</p>
	                                    <p class="mt-2 text-xs text-slate-500">โดย {{ $review->display_name ?: 'ผู้เยี่ยมชม' }} · รายงาน {{ number_format($review->report_count) }} · {{ $review->moderation_reason ?: 'no reason' }}</p>
	                                </td>
                                <td class="px-5 py-4 align-top">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs {{ $statusStyles[$review->status] ?? 'border-white/10 bg-white/5 text-slate-300' }}">
                                        {{ $review->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 align-top">
	                                    <div class="flex justify-end gap-2">
	                                        <a href="{{ route('admin.interactions.reviews.show', $review) }}" class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-white/10">รายละเอียด</a>
	                                        @if($review->status !== 'approved')
	                                            <form method="POST" action="{{ route('admin.interactions.reviews.approve', $review) }}">@csrf @method('PATCH')<button class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">อนุมัติ</button></form>
	                                        @endif
	                                        @if(! in_array($review->status, ['rejected', 'spam'], true))
	                                            <form method="POST" action="{{ route('admin.interactions.reviews.reject', $review) }}">@csrf @method('PATCH')<input type="hidden" name="moderation_reason" value="off_topic"><button class="rounded-xl bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-500">ปฏิเสธ</button></form>
	                                            <form method="POST" action="{{ route('admin.interactions.reviews.spam', $review) }}">@csrf @method('PATCH')<button class="rounded-xl border border-red-400/30 px-3 py-2 text-xs font-semibold text-red-200 hover:bg-red-500/10">สแปม</button></form>
	                                        @endif
	                                    </div>
	                                </td>
                            </tr>
                        @empty
                            <tr>
	                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">ยังไม่มีรีวิว</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $reviews->links() }}
    </div>
</x-layouts.admin>
