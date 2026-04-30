<x-layouts.admin :title="$title" header="รายละเอียดข้อมูลวัด">
    @php
        $content = $temple->content;
        $address = $temple->address;
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $recommendedVisitTime = $temple->recommended_visit_start_time
            ? \Carbon\Carbon::parse($temple->recommended_visit_start_time)->format('H:i')
                . ($temple->recommended_visit_end_time ? ' - ' . \Carbon\Carbon::parse($temple->recommended_visit_end_time)->format('H:i') : '')
            : null;

        $coverUsage = $content?->mediaUsages?->firstWhere('role_key', 'cover');
        $coverUrl = $coverUsage?->media?->path
            ? (filter_var($coverUsage->media->path, FILTER_VALIDATE_URL)
                ? $coverUsage->media->path
                : \Illuminate\Support\Facades\Storage::url($coverUsage->media->path))
            : null;

        $galleryUsages = $content?->mediaUsages?->where('role_key', 'gallery') ?? collect();
    @endphp

    <div class="space-y-6 text-white">

        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        Temple Detail
                    </div>

                    <h1 class="text-2xl font-bold text-white">{{ $content?->title ?? '-' }}</h1>
                    <p class="mt-2 text-sm text-slate-400">slug: {{ $content?->slug ?? '-' }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('admin.temples.edit', $temple) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-amber-950/40 transition hover:opacity-90"
                    >
                        แก้ไข
                    </a>

                    <a
                        href="{{ route('admin.temples.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        กลับไปรายการวัด
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-emerald-950/20">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        @if ($temple->stat)
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-4 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <p class="text-xs font-medium text-slate-400">Score</p>
                    <p class="mt-1 text-2xl font-semibold text-white">{{ number_format($temple->stat->score, 1) }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-4 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <p class="text-xs font-medium text-slate-400">Reviews</p>
                    <p class="mt-1 text-2xl font-semibold text-white">{{ number_format($temple->stat->review_count) }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-4 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <p class="text-xs font-medium text-slate-400">Favorites</p>
                    <p class="mt-1 text-2xl font-semibold text-white">{{ number_format($temple->stat->favorite_count) }}</p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-4 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <p class="text-xs font-medium text-slate-400">Views</p>
                    <p class="mt-1 text-2xl font-semibold text-white">{{ number_format($temple->stat->view_count ?? 0) }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">

                {{-- Cover --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">รูป Cover</h2>
                    </div>

                    @if ($coverUrl)
                        <img
                            src="{{ $coverUrl }}"
                            alt="{{ $content?->title ?? 'Temple cover image' }}"
                            class="h-72 w-full object-cover"
                        >

                        <div class="border-t border-white/10 px-6 py-3 text-xs text-slate-400">
                            {{ $coverUsage?->media?->title ?: $coverUsage?->media?->original_filename }}
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ไม่ได้เลือกรูป Cover</div>
                    @endif
                </section>

                {{-- Gallery --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Gallery</h2>
                    </div>

                    @if ($galleryUsages->isNotEmpty())
                        <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($galleryUsages as $usage)
                                @if ($usage->media?->path)
                                    @php
                                        $galleryUrl = filter_var($usage->media->path, FILTER_VALIDATE_URL)
                                            ? $usage->media->path
                                            : \Illuminate\Support\Facades\Storage::url($usage->media->path);
                                    @endphp

                                    <div class="overflow-hidden rounded-xl border border-white/10 bg-slate-950/50">
                                        <div class="aspect-square overflow-hidden">
                                            <img
                                                src="{{ $galleryUrl }}"
                                                alt="{{ $usage->media->title ?: $usage->media->original_filename }}"
                                                class="h-full w-full object-cover"
                                            >
                                        </div>
                                        <div class="border-t border-white/10 px-3 py-2">
                                            <p class="truncate text-xs text-slate-300">
                                                {{ $usage->media->title ?: $usage->media->original_filename }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีรูป Gallery</div>
                    @endif
                </section>

                {{-- Basic Info --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">ข้อมูลพื้นฐาน</h2>
                    </div>

                    <div class="divide-y divide-white/10">
                        @foreach ([
                            ['ชื่อวัด', $content?->title],
                            ['Slug', $content?->slug],
                            ['ประเภทวัด', $temple->temple_type],
                            ['นิกาย', $temple->sect],
                            ['รูปแบบสถาปัตยกรรม', $temple->architecture_style],
                            ['ปีที่ก่อตั้ง', $temple->founded_year],
                            ['การแต่งกาย', $temple->dress_code],
                            ['เวลาที่แนะนำให้ไป', $recommendedVisitTime],
                        ] as [$label, $value])
                            <div class="grid grid-cols-1 gap-1 px-6 py-3 sm:grid-cols-[180px_minmax(0,1fr)]">
                                <span class="text-sm text-slate-400">{{ $label }}</span>
                                <span class="text-sm text-slate-200">{{ $value ?? '-' }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Content Detail --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">รายละเอียดเนื้อหา</h2>
                    </div>

                    <div class="space-y-5 p-6">
                        <div>
                            <p class="mb-1 text-xs font-medium text-slate-400">คำอธิบายสั้น</p>
                            <p class="text-sm leading-6 text-slate-300">{{ $content?->excerpt ?: '-' }}</p>
                        </div>

                        <div>
                            <p class="mb-1 text-xs font-medium text-slate-400">รายละเอียด</p>
                            <div class="text-sm leading-6 text-slate-300">
                                {!! $content?->description ? nl2br(e($content->description)) : '-' !!}
                            </div>
                        </div>

                        <div>
                            <p class="mb-1 text-xs font-medium text-slate-400">ประวัติ</p>
                            <div class="text-sm leading-6 text-slate-300">
                                {!! $temple->history ? nl2br(e($temple->history)) : '-' !!}
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Opening Hours --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">เวลาเปิด-ปิด</h2>
                    </div>

                    @if ($temple->openingHours->isNotEmpty())
                        <div class="divide-y divide-white/10">
                            @foreach ($temple->openingHours->sortBy('day_of_week') as $hour)
                                <div class="grid grid-cols-1 gap-2 px-6 py-3 sm:grid-cols-[120px_minmax(0,1fr)]">
                                    <span class="text-sm font-medium text-slate-300">
                                        {{ $days[$hour->day_of_week] ?? "Day $hour->day_of_week" }}
                                    </span>

                                    <div class="space-y-1">
                                        @if ($hour->is_closed)
                                            <span class="inline-flex rounded-full border border-rose-400/20 bg-rose-500/10 px-2.5 py-0.5 text-xs font-medium text-rose-300">
                                                ปิด
                                            </span>
                                        @else
                                            <span class="text-sm text-slate-300">
                                                {{ $hour->open_time ? substr($hour->open_time, 0, 5) : '--:--' }}
                                                -
                                                {{ $hour->close_time ? substr($hour->close_time, 0, 5) : '--:--' }}
                                            </span>
                                        @endif

                                        @if ($hour->note)
                                            <p class="text-xs text-slate-500">{{ $hour->note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีข้อมูลเวลาเปิด-ปิด</div>
                    @endif
                </section>

                {{-- Fees --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">ค่าธรรมเนียม</h2>
                    </div>

                    @if ($temple->fees->isNotEmpty())
                        <div class="divide-y divide-white/10">
                            @foreach ($temple->fees->sortBy('sort_order') as $fee)
                                <div class="px-6 py-4">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-white">{{ $fee->label ?: '-' }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                Type: {{ $fee->fee_type ?: '-' }} · Order: {{ $fee->sort_order ?? 0 }}
                                            </p>
                                        </div>

                                        <div class="text-left sm:text-right">
                                            <p class="text-sm font-semibold text-slate-200">
                                                {{ $fee->amount !== null ? number_format($fee->amount, 2) . ' ' . ($fee->currency ?: 'THB') : 'ฟรี' }}
                                            </p>

                                            <span class="mt-1 inline-flex rounded-full border px-2 py-0.5 text-xs {{ $fee->is_active ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300' : 'border-slate-400/20 bg-slate-500/10 text-slate-400' }}">
                                                {{ $fee->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                            </span>
                                        </div>
                                    </div>

                                    @if ($fee->note)
                                        <p class="mt-2 text-sm text-slate-400">{{ $fee->note }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีข้อมูลค่าธรรมเนียม</div>
                    @endif
                </section>

                {{-- Highlights --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">จุดเด่น</h2>
                    </div>

                    @if ($temple->highlights->isNotEmpty())
                        <div class="divide-y divide-white/10">
                            @foreach ($temple->highlights->sortBy('sort_order') as $highlight)
                                <div class="px-6 py-4">
                                    <p class="text-xs text-slate-500">Order: {{ $highlight->sort_order ?? 0 }}</p>
                                    <p class="mt-1 text-sm font-medium text-white">{{ $highlight->title }}</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-400">{{ $highlight->description ?: '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีข้อมูลจุดเด่น</div>
                    @endif
                </section>

                {{-- Visit Rules --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">กฎการเข้าชม</h2>
                    </div>

                    @if ($temple->visitRules->isNotEmpty())
                        <ul class="divide-y divide-white/10">
                            @foreach ($temple->visitRules->sortBy('sort_order') as $rule)
                                <li class="flex items-start gap-3 px-6 py-3">
                                    <span class="mt-0.5 shrink-0 text-blue-300">•</span>
                                    <div>
                                        <p class="text-sm text-slate-300">{{ $rule->rule_text }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Order: {{ $rule->sort_order ?? 0 }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีข้อมูลกฎการเข้าชม</div>
                    @endif
                </section>

                {{-- Travel Infos --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">ข้อมูลการเดินทาง</h2>
                    </div>

                    @if ($temple->travelInfos->isNotEmpty())
                        <div class="divide-y divide-white/10">
                            @foreach ($temple->travelInfos->sortBy('sort_order') as $info)
                                <div class="px-6 py-4 {{ $info->is_active ? '' : 'opacity-60' }}">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-white">{{ $info->travel_type }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                จาก: {{ $info->start_place ?: '-' }} · Order: {{ $info->sort_order ?? 0 }}
                                            </p>
                                        </div>

                                        <span class="inline-flex w-fit rounded-full border px-2 py-0.5 text-xs {{ $info->is_active ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300' : 'border-slate-400/20 bg-slate-500/10 text-slate-400' }}">
                                            {{ $info->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                        </span>
                                    </div>

                                    <div class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                                        <div>
                                            <p class="text-xs text-slate-500">ระยะทาง</p>
                                            <p class="text-slate-300">{{ $info->distance_km !== null ? number_format($info->distance_km, 1) . ' กม.' : '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">เวลาเดินทาง</p>
                                            <p class="text-slate-300">{{ $info->duration_minutes !== null ? $info->duration_minutes . ' นาที' : '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">ค่าใช้จ่าย</p>
                                            <p class="text-slate-300">{{ $info->cost_estimate ?: '-' }}</p>
                                        </div>
                                    </div>

                                    @if ($info->note)
                                        <p class="mt-3 text-sm text-slate-400">{{ $info->note }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีข้อมูลการเดินทาง</div>
                    @endif
                </section>

                {{-- Nearby Places --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">วัดใกล้เคียง</h2>
                    </div>

                    @if ($temple->nearbyPlaces->isNotEmpty())
                        <div class="divide-y divide-white/10">
                            @foreach ($temple->nearbyPlaces->sortBy('sort_order') as $nearby)
                                <div class="px-6 py-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-white">
                                                {{ $nearby->nearbyTemple?->content?->title ?? "Temple #$nearby->nearby_temple_id" }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                Relation: {{ $nearby->relation_type ?: '-' }} · Order: {{ $nearby->sort_order ?? 0 }}
                                            </p>
                                        </div>

                                        <a
                                            href="{{ route('admin.temples.show', $nearby->nearby_temple_id) }}"
                                            class="w-fit rounded-lg border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs text-slate-300 transition hover:bg-white/10 hover:text-white"
                                        >
                                            ดู
                                        </a>
                                    </div>

                                    <div class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                                        <div>
                                            <p class="text-xs text-slate-500">ระยะทาง</p>
                                            <p class="text-slate-300">{{ $nearby->distance_km !== null ? number_format($nearby->distance_km, 1) . ' กม.' : '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">เวลาเดินทาง</p>
                                            <p class="text-slate-300">{{ $nearby->duration_minutes !== null ? $nearby->duration_minutes . ' นาที' : '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">คะแนน</p>
                                            <p class="text-slate-300">{{ $nearby->score !== null ? number_format($nearby->score, 1) : '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีข้อมูลวัดใกล้เคียง</div>
                    @endif
                </section>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">

                {{-- Status --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">สถานะและการเผยแพร่</h2>
                    </div>

                    @php
                        $statusClass = match($content?->status) {
                            'published' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300',
                            'draft' => 'border-amber-400/20 bg-amber-500/10 text-amber-300',
                            'archived' => 'border-slate-400/20 bg-slate-500/10 text-slate-300',
                            default => 'border-white/10 bg-white/[0.04] text-slate-300',
                        };
                    @endphp

                    <div class="divide-y divide-white/10">
                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-400">Status</span>
                            <span class="rounded-full border px-3 py-1 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($content?->status ?? '-') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-400">Featured</span>
                            <span class="text-sm font-medium {{ $content?->is_featured ? 'text-emerald-300' : 'text-slate-500' }}">
                                {{ $content?->is_featured ? 'Yes' : 'No' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-400">Popular</span>
                            <span class="text-sm font-medium {{ $content?->is_popular ? 'text-emerald-300' : 'text-slate-500' }}">
                                {{ $content?->is_popular ? 'Yes' : 'No' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Published At</span>
                            <span class="text-right text-sm text-slate-300">{{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Created</span>
                            <span class="text-right text-sm text-slate-300">{{ $content?->created_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Updated</span>
                            <span class="text-right text-sm text-slate-300">{{ $content?->updated_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </section>

                {{-- Categories --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">หมวดหมู่</h2>
                    </div>

                    @if ($content?->categories?->isNotEmpty())
                        <div class="flex flex-wrap gap-2 p-6">
                            @foreach ($content->categories as $cat)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-slate-950/40 px-3 py-1 text-xs font-medium text-slate-300">
                                    {{ $cat->name }}

                                    @if ($cat->pivot->is_primary)
                                        <span class="rounded-full bg-blue-600 px-1.5 py-0.5 text-[10px] text-white">Primary</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่ได้เลือกหมวดหมู่</div>
                    @endif
                </section>

                {{-- Address --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">ที่ตั้ง</h2>
                    </div>

                    @if ($address)
                        <div class="divide-y divide-white/10">
                            @foreach ([
                                ['Address Line', $address->address_line],
                                ['แขวง / ตำบล', $address->subdistrict],
                                ['เขต / อำเภอ', $address->district],
                                ['จังหวัด', $address->province],
                                ['รหัสไปรษณีย์', $address->postal_code],
                                ['Latitude', $address->latitude],
                                ['Longitude', $address->longitude],
                                ['Google Place ID', $address->google_place_id],
                            ] as [$label, $value])
                                <div class="px-6 py-3">
                                    <p class="text-xs text-slate-500">{{ $label }}</p>
                                    <p class="mt-0.5 break-words text-sm text-slate-300">{{ $value ?: '-' }}</p>
                                </div>
                            @endforeach

                            <div class="px-6 py-3">
                                <p class="text-xs text-slate-500">Google Maps URL</p>

                                @if ($address->google_maps_url)
                                    <a
                                        href="{{ $address->google_maps_url }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="mt-0.5 inline-block break-all text-sm font-medium text-blue-300 underline underline-offset-2 hover:text-blue-200"
                                    >
                                        {{ $address->google_maps_url }}
                                    </a>
                                @else
                                    <p class="mt-0.5 text-sm text-slate-300">-</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีข้อมูลที่ตั้ง</div>
                    @endif
                </section>

                {{-- Facilities --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">สิ่งอำนวยความสะดวก</h2>
                    </div>

                    @if ($temple->facilityItems->isNotEmpty())
                        <div class="divide-y divide-white/10">
                            @foreach ($temple->facilityItems->sortBy('sort_order') as $item)
                                <div class="px-6 py-3">
                                    <p class="text-sm font-medium text-slate-200">
                                        {{ $item->facility?->name ?? "Facility #$item->facility_id" }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-300">ค่า: {{ $item->value ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">หมายเหตุ: {{ $item->note ?: '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-sm text-slate-400">ยังไม่มีข้อมูลสิ่งอำนวยความสะดวก</div>
                    @endif
                </section>

                {{-- SEO --}}
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">SEO</h2>
                    </div>

                    <div class="space-y-4 p-6">
                        <div>
                            <p class="text-xs font-medium text-slate-500">Meta Title</p>
                            <p class="mt-0.5 text-sm text-slate-200">{{ $content?->meta_title ?: '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-slate-500">Meta Description</p>
                            <p class="mt-0.5 text-sm text-slate-300">{{ $content?->meta_description ?: '-' }}</p>
                        </div>
                    </div>
                </section>

                {{-- Danger Zone --}}
                <section class="overflow-hidden rounded-2xl border border-rose-400/20 bg-rose-500/10 shadow-xl shadow-rose-950/20 backdrop-blur">
                    <div class="border-b border-rose-400/20 px-6 py-4">
                        <h2 class="text-base font-semibold text-rose-200">Danger Zone</h2>
                    </div>

                    <div class="p-6">
                        <p class="mb-4 text-sm text-rose-200/80">การลบข้อมูลวัดจะไม่สามารถกู้คืนได้</p>

                        <form
                            method="POST"
                            action="{{ route('admin.temples.destroy', $temple) }}"
                            onsubmit="return confirm('ยืนยันการลบข้อมูลวัดนี้?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-2.5 text-sm font-medium text-rose-200 transition hover:bg-rose-500/20"
                            >
                                ลบข้อมูลวัด
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.admin>