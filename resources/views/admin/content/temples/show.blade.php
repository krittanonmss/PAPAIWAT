<x-layouts.admin :title="$title" header="Temple Detail">
    @php
        $content = $temple->content;
        $address = $temple->address;
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    @endphp

    <div class="space-y-6">

        {{-- ── Header ─────────────────────────────────────────── --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $content?->title ?? '-' }}</h1>
                <p class="mt-0.5 text-sm text-slate-500">slug: {{ $content?->slug ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.temples.edit', $temple) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Edit
                </a>
                <a
                    href="{{ route('admin.temples.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Back to List
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Stats Bar ───────────────────────────────────────── --}}
        @if ($temple->stat)
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-medium text-slate-500">Score</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($temple->stat->score, 1) }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-medium text-slate-500">Reviews</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($temple->stat->review_count) }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-medium text-slate-500">Favorites</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($temple->stat->favorite_count) }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-medium text-slate-500">Views</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($temple->stat->view_count ?? 0) }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- ── Left Column ─────────────────────────────────── --}}
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
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <img
                            src="{{ $coverUrl }}"
                            alt="{{ $content->title }}"
                            class="h-64 w-full object-cover"
                        >
                    </div>
                @endif

                {{-- Gallery --}}
                @php $galleryUsages = $content?->mediaUsages->where('role_key', 'gallery'); @endphp
                @if ($galleryUsages?->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Gallery</h2>
                        </div>
                        <div class="grid grid-cols-3 gap-2 p-4 sm:grid-cols-4">
                            @foreach ($galleryUsages as $usage)
                                @if ($usage->media?->path)
                                    @php
                                        $galleryUrl = filter_var($usage->media->path, FILTER_VALIDATE_URL)
                                            ? $usage->media->path
                                            : \Illuminate\Support\Facades\Storage::url($usage->media->path);
                                    @endphp
                                    <div class="aspect-square overflow-hidden rounded-xl bg-slate-100">
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
                <div class="rounded-2xl border border-slate-200 bg-white">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Basic Information</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @php
                            $basicRows = [
                                ['Temple Type',        $temple->temple_type],
                                ['Sect / นิกาย',       $temple->sect],
                                ['Architecture Style', $temple->architecture_style],
                                ['Founded Year',       $temple->founded_year],
                                ['Dress Code',         $temple->dress_code],
                                ['Recommended Duration', $temple->recommended_visit_duration_minutes ? $temple->recommended_visit_duration_minutes . ' นาที' : null],
                            ];
                        @endphp
                        @foreach ($basicRows as [$label, $value])
                            <div class="flex gap-4 px-6 py-3">
                                <span class="w-44 shrink-0 text-sm text-slate-500">{{ $label }}</span>
                                <span class="text-sm text-slate-900">{{ $value ?? '-' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Excerpt & Description --}}
                @if ($content?->excerpt || $content?->description)
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Description</h2>
                        </div>
                        <div class="space-y-4 p-6">
                            @if ($content->excerpt)
                                <div>
                                    <p class="mb-1 text-xs font-medium text-slate-500">Excerpt</p>
                                    <p class="text-sm text-slate-700">{{ $content->excerpt }}</p>
                                </div>
                            @endif
                            @if ($content->description)
                                <div>
                                    <p class="mb-1 text-xs font-medium text-slate-500">Description</p>
                                    <div class="prose prose-sm max-w-none text-slate-700">{!! nl2br(e($content->description)) !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- History --}}
                @if ($temple->history)
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">History / ประวัติ</h2>
                        </div>
                        <div class="p-6">
                            <div class="prose prose-sm max-w-none text-slate-700">{!! nl2br(e($temple->history)) !!}</div>
                        </div>
                    </div>
                @endif

                {{-- Opening Hours --}}
                @if ($temple->openingHours->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Opening Hours</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($temple->openingHours->sortBy('day_of_week') as $hour)
                                <div class="flex items-center gap-4 px-6 py-3">
                                    <span class="w-28 shrink-0 text-sm font-medium text-slate-700">
                                        {{ $days[$hour->day_of_week] ?? "Day $hour->day_of_week" }}
                                    </span>
                                    @if ($hour->is_closed)
                                        <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-700">Closed</span>
                                    @else
                                        <span class="text-sm text-slate-600">
                                            {{ $hour->open_time ? substr($hour->open_time, 0, 5) : '--:--' }}
                                            –
                                            {{ $hour->close_time ? substr($hour->close_time, 0, 5) : '--:--' }}
                                        </span>
                                    @endif
                                    @if ($hour->note)
                                        <span class="text-xs text-slate-400">{{ $hour->note }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Fees --}}
                @if ($temple->fees->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Fees / ค่าธรรมเนียม</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Type</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Label</th>
                                        <th class="px-6 py-3 text-right font-semibold text-slate-600">Amount</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Note</th>
                                        <th class="px-6 py-3 text-center font-semibold text-slate-600">Active</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($temple->fees->sortBy('sort_order') as $fee)
                                        <tr>
                                            <td class="px-6 py-3 text-slate-600">{{ $fee->fee_type }}</td>
                                            <td class="px-6 py-3 text-slate-900">{{ $fee->label }}</td>
                                            <td class="px-6 py-3 text-right text-slate-900">
                                                {{ $fee->amount !== null ? number_format($fee->amount, 2) . ' ' . $fee->currency : 'ฟรี' }}
                                            </td>
                                            <td class="px-6 py-3 text-slate-500">{{ $fee->note ?? '-' }}</td>
                                            <td class="px-6 py-3 text-center">
                                                @if ($fee->is_active)
                                                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                                                @else
                                                    <span class="inline-block h-2 w-2 rounded-full bg-slate-300"></span>
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
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Highlights</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($temple->highlights->sortBy('sort_order') as $highlight)
                                <div class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-900">{{ $highlight->title }}</p>
                                    @if ($highlight->description)
                                        <p class="mt-1 text-sm text-slate-500">{{ $highlight->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Visit Rules --}}
                @if ($temple->visitRules->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Visit Rules / กฎการเข้าชม</h2>
                        </div>
                        <ul class="divide-y divide-slate-100">
                            @foreach ($temple->visitRules->sortBy('sort_order') as $rule)
                                <li class="flex items-start gap-3 px-6 py-3">
                                    <span class="mt-0.5 shrink-0 text-slate-400">•</span>
                                    <span class="text-sm text-slate-700">{{ $rule->rule_text }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Travel Infos --}}
                @if ($temple->travelInfos->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Travel Information</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Type</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-600">From</th>
                                        <th class="px-6 py-3 text-right font-semibold text-slate-600">Distance</th>
                                        <th class="px-6 py-3 text-right font-semibold text-slate-600">Duration</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Cost</th>
                                        <th class="px-6 py-3 text-left font-semibold text-slate-600">Note</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($temple->travelInfos->sortBy('sort_order') as $info)
                                        <tr class="{{ $info->is_active ? '' : 'opacity-50' }}">
                                            <td class="px-6 py-3 font-medium text-slate-900">{{ $info->travel_type }}</td>
                                            <td class="px-6 py-3 text-slate-600">{{ $info->start_place ?? '-' }}</td>
                                            <td class="px-6 py-3 text-right text-slate-600">
                                                {{ $info->distance_km !== null ? number_format($info->distance_km, 1) . ' km' : '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-right text-slate-600">
                                                {{ $info->duration_minutes !== null ? $info->duration_minutes . ' นาที' : '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-slate-600">{{ $info->cost_estimate ?? '-' }}</td>
                                            <td class="px-6 py-3 text-slate-500">{{ $info->note ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Nearby Places --}}
                @if ($temple->nearbyPlaces->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Nearby Places</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($temple->nearbyPlaces->sortBy('sort_order') as $nearby)
                                <div class="flex items-center justify-between gap-4 px-6 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">
                                            {{ $nearby->nearbyTemple?->content?->title ?? "Temple #$nearby->nearby_temple_id" }}
                                        </p>
                                        @if ($nearby->relation_type)
                                            <p class="mt-0.5 text-xs text-slate-500">{{ $nearby->relation_type }}</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 items-center gap-4 text-right text-xs text-slate-500">
                                        @if ($nearby->distance_km !== null)
                                            <span>{{ number_format($nearby->distance_km, 1) }} km</span>
                                        @endif
                                        @if ($nearby->duration_minutes !== null)
                                            <span>{{ $nearby->duration_minutes }} นาที</span>
                                        @endif
                                        @if ($nearby->score !== null)
                                            <span class="font-medium text-slate-700">{{ $nearby->score }}</span>
                                        @endif
                                        <a
                                            href="{{ route('admin.temples.show', $nearby->nearby_temple_id) }}"
                                            class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs text-slate-600 hover:bg-slate-50"
                                        >
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>{{-- /left --}}

            {{-- ── Right Column ─────────────────────────────────── --}}
            <div class="space-y-6">

                {{-- Status Card --}}
                <div class="rounded-2xl border border-slate-200 bg-white">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-slate-900">Status & Visibility</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-500">Status</span>
                            @php
                                $statusColor = match($content?->status) {
                                    'published' => 'bg-emerald-100 text-emerald-700',
                                    'draft'     => 'bg-amber-100 text-amber-700',
                                    'archived'  => 'bg-slate-100 text-slate-600',
                                    default     => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusColor }}">
                                {{ ucfirst($content?->status ?? '-') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-500">Featured</span>
                            <span class="text-sm font-medium {{ $content?->is_featured ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $content?->is_featured ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-500">Popular</span>
                            <span class="text-sm font-medium {{ $content?->is_popular ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $content?->is_popular ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-500">Published At</span>
                            <span class="text-sm text-slate-700">{{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-500">Created</span>
                            <span class="text-sm text-slate-700">{{ $content?->created_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between px-6 py-3">
                            <span class="text-sm text-slate-500">Updated</span>
                            <span class="text-sm text-slate-700">{{ $content?->updated_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Categories --}}
                @if ($content?->categories->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Categories</h2>
                        </div>
                        <div class="flex flex-wrap gap-2 p-6">
                            @foreach ($content->categories as $cat)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700">
                                    {{ $cat->name }}
                                    @if ($cat->pivot->is_primary)
                                        <span class="rounded-full bg-slate-900 px-1.5 py-0.5 text-[10px] text-white">Primary</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Address --}}
                @if ($address)
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Address</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @if ($address->address_line)
                                <div class="px-6 py-3">
                                    <p class="text-xs text-slate-500">Address</p>
                                    <p class="mt-0.5 text-sm text-slate-900">{{ $address->address_line }}</p>
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
                                    <p class="mt-0.5 text-sm text-slate-900">{{ implode(', ', $addrParts) }}</p>
                                </div>
                            @endif
                            @if ($address->latitude && $address->longitude)
                                <div class="px-6 py-3">
                                    <p class="text-xs text-slate-500">Coordinates</p>
                                    <p class="mt-0.5 font-mono text-sm text-slate-900">
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
                                        class="text-sm font-medium text-slate-700 underline underline-offset-2 hover:text-slate-900"
                                    >
                                        Open in Google Maps ↗
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Facilities --}}
                @if ($temple->facilityItems->isNotEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">Facilities</h2>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach ($temple->facilityItems->sortBy('sort_order') as $item)
                                <div class="flex items-center justify-between gap-3 px-6 py-3">
                                    <span class="text-sm text-slate-700">{{ $item->facility?->name ?? "Facility #$item->facility_id" }}</span>
                                    <div class="text-right">
                                        @if ($item->value)
                                            <span class="text-sm font-medium text-slate-900">{{ $item->value }}</span>
                                        @endif
                                        @if ($item->note)
                                            <p class="text-xs text-slate-400">{{ $item->note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- SEO --}}
                @if ($content?->meta_title || $content?->meta_description)
                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="border-b border-slate-100 px-6 py-4">
                            <h2 class="text-base font-semibold text-slate-900">SEO</h2>
                        </div>
                        <div class="space-y-3 p-6">
                            @if ($content->meta_title)
                                <div>
                                    <p class="text-xs font-medium text-slate-500">Meta Title</p>
                                    <p class="mt-0.5 text-sm text-slate-900">{{ $content->meta_title }}</p>
                                </div>
                            @endif
                            @if ($content->meta_description)
                                <div>
                                    <p class="text-xs font-medium text-slate-500">Meta Description</p>
                                    <p class="mt-0.5 text-sm text-slate-700">{{ $content->meta_description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Danger Zone --}}
                <div class="rounded-2xl border border-rose-200 bg-white">
                    <div class="border-b border-rose-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-rose-700">Danger Zone</h2>
                    </div>
                    <div class="p-6">
                        <p class="mb-4 text-sm text-slate-500">การลบข้อมูลวัดจะไม่สามารถกู้คืนได้</p>
                        <form
                            method="POST"
                            action="{{ route('admin.temples.destroy', $temple) }}"
                            onsubmit="return confirm('ยืนยันการลบข้อมูลวัดนี้?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="w-full rounded-xl border border-rose-300 px-4 py-2.5 text-sm font-medium text-rose-700 hover:bg-rose-50"
                            >
                                Delete Temple
                            </button>
                        </form>
                    </div>
                </div>

            </div>{{-- /right --}}

        </div>
    </div>
</x-layouts.admin>