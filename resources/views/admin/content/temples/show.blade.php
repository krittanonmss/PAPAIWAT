<x-layouts.admin :title="$title" header="รายละเอียดข้อมูลวัด">
    @php
        $content = $temple->content;
        $address = $temple->address;
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $recommendedVisitTime = $temple->recommended_visit_start_time
            ? \Carbon\Carbon::parse($temple->recommended_visit_start_time)->format('H:i')
                . ($temple->recommended_visit_end_time ? ' - ' . \Carbon\Carbon::parse($temple->recommended_visit_end_time)->format('H:i') : '')
            : null;
    @endphp

    <div class="space-y-6 text-white">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-300">Temple Detail</p>
                <h1 class="mt-1 text-2xl font-bold text-white">{{ $content?->title ?? '-' }}</h1>
                <p class="mt-1 text-sm text-slate-400">slug: {{ $content?->slug ?? '-' }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('admin.temples.edit', $temple) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
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

            {{-- Left Column --}}
            <div class="space-y-6 xl:col-span-2">

                {{-- Cover Image --}}
                @php
                    $coverUsage = $content?->mediaUsages->firstWhere('role_key', 'cover');
                    $coverUrl = $coverUsage?->media?->path
                        ? (filter_var($coverUsage->media->path, FILTER_VALIDATE_URL)
                            ? $coverUsage->media->path
                            : \Illuminate\Support\Facades\Storage::url($coverUsage->media->path))
                        : null;
                @endphp

                @if ($coverUrl)
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <img
                            src="{{ $coverUrl }}"
                            alt="{{ $content?->title ?? 'Temple cover image' }}"
                            class="h-72 w-full object-cover"
                        >
                    </div>
                @endif

                {{-- Gallery --}}
                @php $galleryUsages = $content?->mediaUsages->where('role_key', 'gallery'); @endphp
                @if ($galleryUsages?->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">Gallery</h2>
                        </div>

                        <div class="grid grid-cols-3 gap-2 p-4 sm:grid-cols-4">
                            @foreach ($galleryUsages as $usage)
                                @if ($usage->media?->path)
                                    @php
                                        $galleryUrl = filter_var($usage->media->path, FILTER_VALIDATE_URL)
                                            ? $usage->media->path
                                            : \Illuminate\Support\Facades\Storage::url($usage->media->path);
                                    @endphp

                                    <div class="aspect-square overflow-hidden rounded-xl bg-slate-950/60">
                                        <img
                                            src="{{ $galleryUrl }}"
                                            alt="{{ $usage->media->title ?: $usage->media->original_filename }}"
                                            class="h-full w-full object-cover"
                                        >
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Basic Info --}}
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">ข้อมูลพื้นฐาน</h2>
                    </div>

                    <div class="divide-y divide-white/10">
                        @php
                            $basicRows = [
                                ['ประเภทวัด', $temple->temple_type],
                                ['นิกาย', $temple->sect],
                                ['รูปแบบสถาปัตยกรรม', $temple->architecture_style],
                                ['ปีที่ก่อตั้ง', $temple->founded_year],
                                ['การแต่งกาย', $temple->dress_code],
                                ['เวลาที่แนะนำให้ไป', $recommendedVisitTime],
                            ];
                        @endphp

                        @foreach ($basicRows as [$label, $value])
                            <div class="flex gap-4 px-6 py-3">
                                <span class="w-44 shrink-0 text-sm text-slate-400">{{ $label }}</span>
                                <span class="text-sm text-slate-200">{{ $value ?? '-' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Description --}}
                @if ($content?->excerpt || $content?->description)
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">รายละเอียด</h2>
                        </div>

                        <div class="space-y-4 p-6">
                            @if ($content->excerpt)
                                <div>
                                    <p class="mb-1 text-xs font-medium text-slate-400">คำอธิบายสั้น</p>
                                    <p class="text-sm leading-6 text-slate-300">{{ $content->excerpt }}</p>
                                </div>
                            @endif

                            @if ($content->description)
                                <div>
                                    <p class="mb-1 text-xs font-medium text-slate-400">รายละเอียด</p>
                                    <div class="text-sm leading-6 text-slate-300">{!! nl2br(e($content->description)) !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- History --}}
                @if ($temple->history)
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">ประวัติ</h2>
                        </div>

                        <div class="p-6">
                            <div class="text-sm leading-6 text-slate-300">{!! nl2br(e($temple->history)) !!}</div>
                        </div>
                    </div>
                @endif

                {{-- Opening Hours --}}
                @if ($temple->openingHours->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">เวลาเปิด-ปิด</h2>
                        </div>

                        <div class="divide-y divide-white/10">
                            @foreach ($temple->openingHours->sortBy('day_of_week') as $hour)
                                <div class="flex items-center gap-4 px-6 py-3">
                                    <span class="w-28 shrink-0 text-sm font-medium text-slate-300">
                                        {{ $days[$hour->day_of_week] ?? "Day $hour->day_of_week" }}
                                    </span>

                                    @if ($hour->is_closed)
                                        <span class="rounded-full border border-rose-400/20 bg-rose-500/10 px-2.5 py-0.5 text-xs font-medium text-rose-300">
                                            Closed
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-300">
                                            {{ $hour->open_time ? substr($hour->open_time, 0, 5) : '--:--' }}
                                            –
                                            {{ $hour->close_time ? substr($hour->close_time, 0, 5) : '--:--' }}
                                        </span>
                                    @endif

                                    @if ($hour->note)
                                        <span class="text-xs text-slate-500">{{ $hour->note }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Fees --}}
                @if ($temple->fees->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">ค่าธรรมเนียม</h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-white/10 text-sm">
                                <thead class="bg-slate-950/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-300">Type</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-300">Label</th>
                                        <th class="px-6 py-3 text-right font-semibold text-slate-300">Amount</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-300">Note</th>
                                        <th class="px-6 py-3 text-center font-semibold text-slate-300">Active</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-white/10">
                                    @foreach ($temple->fees->sortBy('sort_order') as $fee)
                                        <tr class="hover:bg-white/[0.03]">
                                            <td class="px-6 py-3 text-slate-300">{{ $fee->fee_type }}</td>
                                            <td class="px-6 py-3 text-white">{{ $fee->label }}</td>
                                            <td class="px-6 py-3 text-right text-slate-200">
                                                {{ $fee->amount !== null ? number_format($fee->amount, 2) . ' ' . $fee->currency : 'ฟรี' }}
                                            </td>
                                            <td class="px-6 py-3 text-slate-400">{{ $fee->note ?? '-' }}</td>
                                            <td class="px-6 py-3 text-center">
                                                @if ($fee->is_active)
                                                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-400"></span>
                                                @else
                                                    <span class="inline-block h-2 w-2 rounded-full bg-slate-500"></span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Highlights --}}
                @if ($temple->highlights->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">จุดเด่น</h2>
                        </div>

                        <div class="divide-y divide-white/10">
                            @foreach ($temple->highlights->sortBy('sort_order') as $highlight)
                                <div class="px-6 py-4">
                                    <p class="text-sm font-medium text-white">{{ $highlight->title }}</p>
                                    @if ($highlight->description)
                                        <p class="mt-1 text-sm text-slate-400">{{ $highlight->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Visit Rules --}}
                @if ($temple->visitRules->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">กฎการเข้าชม</h2>
                        </div>

                        <ul class="divide-y divide-white/10">
                            @foreach ($temple->visitRules->sortBy('sort_order') as $rule)
                                <li class="flex items-start gap-3 px-6 py-3">
                                    <span class="mt-0.5 shrink-0 text-blue-300">•</span>
                                    <span class="text-sm text-slate-300">{{ $rule->rule_text }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Travel Infos --}}
                @if ($temple->travelInfos->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">ข้อมูลการเดินทาง</h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-white/10 text-sm">
                                <thead class="bg-slate-950/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-300">Type</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-300">From</th>
                                        <th class="px-6 py-3 text-right font-semibold text-slate-300">Distance</th>
                                        <th class="px-6 py-3 text-right font-semibold text-slate-300">Duration</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-300">Cost</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-300">Note</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-white/10">
                                    @foreach ($temple->travelInfos->sortBy('sort_order') as $info)
                                        <tr class="{{ $info->is_active ? 'hover:bg-white/[0.03]' : 'opacity-50' }}">
                                            <td class="px-6 py-3 font-medium text-white">{{ $info->travel_type }}</td>
                                            <td class="px-6 py-3 text-slate-300">{{ $info->start_place ?? '-' }}</td>
                                            <td class="px-6 py-3 text-right text-slate-300">
                                                {{ $info->distance_km !== null ? number_format($info->distance_km, 1) . ' km' : '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-right text-slate-300">
                                                {{ $info->duration_minutes !== null ? $info->duration_minutes . ' นาที' : '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-slate-300">{{ $info->cost_estimate ?? '-' }}</td>
                                            <td class="px-6 py-3 text-slate-400">{{ $info->note ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Nearby Places --}}
                @if ($temple->nearbyPlaces->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">วัดใกล้เคียง</h2>
                        </div>

                        <div class="divide-y divide-white/10">
                            @foreach ($temple->nearbyPlaces->sortBy('sort_order') as $nearby)
                                <div class="flex items-center justify-between gap-4 px-6 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-white">
                                            {{ $nearby->nearbyTemple?->content?->title ?? "Temple #$nearby->nearby_temple_id" }}
                                        </p>

                                        @if ($nearby->relation_type)
                                            <p class="mt-0.5 text-xs text-slate-500">{{ $nearby->relation_type }}</p>
                                        @endif
                                    </div>

                                    <div class="flex shrink-0 items-center gap-4 text-right text-xs text-slate-400">
                                        @if ($nearby->distance_km !== null)
                                            <span>{{ number_format($nearby->distance_km, 1) }} km</span>
                                        @endif

                                        @if ($nearby->duration_minutes !== null)
                                            <span>{{ $nearby->duration_minutes }} นาที</span>
                                        @endif

                                        @if ($nearby->score !== null)
                                            <span class="font-medium text-slate-300">{{ $nearby->score }}</span>
                                        @endif

                                        <a
                                            href="{{ route('admin.temples.show', $nearby->nearby_temple_id) }}"
                                            class="rounded-lg border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs text-slate-300 transition hover:bg-white/10 hover:text-white"
                                        >
                                            ดู
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Right Column --}}
            <div class="space-y-6">

                {{-- Status Card --}}
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">สถานะและการเผยแพร่</h2>
                    </div>

                    <div class="divide-y divide-white/10">
                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-400">Status</span>

                            @php
                                $statusClass = match($content?->status) {
                                    'published' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300',
                                    'draft' => 'border-amber-400/20 bg-amber-500/10 text-amber-300',
                                    'archived' => 'border-slate-400/20 bg-slate-500/10 text-slate-300',
                                    default => 'border-white/10 bg-white/[0.04] text-slate-300',
                                };
                            @endphp

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
                </div>

                {{-- Categories --}}
                @if ($content?->categories->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">หมวดหมู่</h2>
                        </div>

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
                    </div>
                @endif

                {{-- Address --}}
                @if ($address)
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">ที่ตั้ง</h2>
                        </div>

                        <div class="divide-y divide-white/10">
                            @if ($address->address_line)
                                <div class="px-6 py-3">
                                    <p class="text-xs text-slate-500">Address</p>
                                    <p class="mt-0.5 text-sm text-slate-300">{{ $address->address_line }}</p>
                                </div>
                            @endif

                            @php
                                $addrParts = array_filter([
                                    $address->subdistrict,
                                    $address->district,
                                    $address->province,
                                    $address->postal_code,
                                ]);
                            @endphp

                            @if ($addrParts)
                                <div class="px-6 py-3">
                                    <p class="text-xs text-slate-500">Area</p>
                                    <p class="mt-0.5 text-sm text-slate-300">{{ implode(', ', $addrParts) }}</p>
                                </div>
                            @endif

                            @if ($address->latitude && $address->longitude)
                                <div class="px-6 py-3">
                                    <p class="text-xs text-slate-500">Coordinates</p>
                                    <p class="mt-0.5 font-mono text-sm text-slate-300">
                                        {{ $address->latitude }}, {{ $address->longitude }}
                                    </p>
                                </div>
                            @endif

                            @if ($address->google_maps_url)
                                <div class="px-6 py-3">
                                    <a
                                        href="{{ $address->google_maps_url }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="text-sm font-medium text-blue-300 underline underline-offset-2 hover:text-blue-200"
                                    >
                                        เปิดใน Google Maps ↗
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Facilities --}}
                @if ($temple->facilityItems->isNotEmpty())
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">สิ่งอำนวยความสะดวก</h2>
                        </div>

                        <div class="divide-y divide-white/10">
                            @foreach ($temple->facilityItems->sortBy('sort_order') as $item)
                                <div class="flex items-center justify-between gap-3 px-6 py-3">
                                    <span class="text-sm text-slate-300">{{ $item->facility?->name ?? "Facility #$item->facility_id" }}</span>

                                    <div class="text-right">
                                        @if ($item->value)
                                            <span class="text-sm font-medium text-white">{{ $item->value }}</span>
                                        @endif

                                        @if ($item->note)
                                            <p class="text-xs text-slate-500">{{ $item->note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- SEO --}}
                @if ($content?->meta_title || $content?->meta_description)
                    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 px-6 py-4">
                            <h2 class="text-base font-semibold text-white">SEO</h2>
                        </div>

                        <div class="space-y-3 p-6">
                            @if ($content->meta_title)
                                <div>
                                    <p class="text-xs font-medium text-slate-500">Meta Title</p>
                                    <p class="mt-0.5 text-sm text-slate-200">{{ $content->meta_title }}</p>
                                </div>
                            @endif

                            @if ($content->meta_description)
                                <div>
                                    <p class="text-xs font-medium text-slate-500">Meta Description</p>
                                    <p class="mt-0.5 text-sm text-slate-300">{{ $content->meta_description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Danger Zone --}}
                <div class="overflow-hidden rounded-2xl border border-rose-400/20 bg-rose-500/10 shadow-xl shadow-rose-950/20 backdrop-blur">
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
                </div>

            </div>
        </div>
    </div>
</x-layouts.admin>