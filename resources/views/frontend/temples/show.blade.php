@php
    $content = $temple->content;
    $address = $temple->address;
    $stat = $temple->stat;

    $days = [
        0 => 'อาทิตย์',
        1 => 'จันทร์',
        2 => 'อังคาร',
        3 => 'พุธ',
        4 => 'พฤหัสบดี',
        5 => 'ศุกร์',
        6 => 'เสาร์',
    ];

    $coverUsage = $content?->mediaUsages?->firstWhere('role_key', 'cover');
    $coverUrl = $coverUsage?->media?->path
        ? (filter_var($coverUsage->media->path, FILTER_VALIDATE_URL)
            ? $coverUsage->media->path
            : \Illuminate\Support\Facades\Storage::url($coverUsage->media->path))
        : null;

    $galleryUsages = $content?->mediaUsages?->where('role_key', 'gallery') ?? collect();
@endphp

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content?->meta_title ?? $content?->title ?? 'Temple Detail' }}</title>
    <meta name="description" content="{{ $content?->meta_description ?? $content?->excerpt }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white">
    <main>
        {{-- Hero --}}
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/30 via-slate-950/70 to-slate-950"></div>

            @if ($coverUrl)
                <img
                    src="{{ $coverUrl }}"
                    alt="{{ $content?->title ?? 'Temple cover image' }}"
                    class="h-[520px] w-full object-cover"
                >
            @else
                <div class="h-[420px] w-full bg-slate-900"></div>
            @endif

            <div class="absolute inset-x-0 bottom-0">
                <div class="mx-auto max-w-6xl px-4 pb-12">
                    <div class="max-w-3xl">
                        <p class="mb-3 text-sm font-medium text-blue-300">
                            {{ $address?->province ?? 'Temple Detail' }}
                        </p>

                        <h1 class="text-4xl font-bold text-white md:text-6xl">
                            {{ $content?->title ?? '-' }}
                        </h1>

                        @if ($content?->excerpt)
                            <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300">
                                {{ $content->excerpt }}
                            </p>
                        @endif

                        <div class="mt-6 flex flex-wrap gap-3">
                            @if ($temple->temple_type)
                                <span class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-sm text-slate-200">
                                    {{ $temple->temple_type }}
                                </span>
                            @endif

                            @if ($temple->sect)
                                <span class="rounded-full border border-white/10 bg-white/[0.08] px-3 py-1 text-sm text-slate-200">
                                    {{ $temple->sect }}
                                </span>
                            @endif

                            @if ($stat)
                                <span class="rounded-full border border-amber-400/20 bg-amber-500/10 px-3 py-1 text-sm text-amber-300">
                                    ⭐ {{ number_format($stat->score, 1) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-12">
            <div class="grid gap-8 lg:grid-cols-3">
                {{-- Main --}}
                <div class="space-y-8 lg:col-span-2">
                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-xl font-semibold text-white">รายละเอียด</h2>

                        <div class="mt-5 space-y-5 text-sm leading-7 text-slate-300">
                            @if ($content?->description)
                                <p>{!! nl2br(e($content->description)) !!}</p>
                            @endif

                            @if ($temple->history)
                                <div>
                                    <h3 class="mb-2 font-semibold text-white">ประวัติ</h3>
                                    <p>{!! nl2br(e($temple->history)) !!}</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    @if ($temple->highlights->isNotEmpty())
                        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                            <h2 class="text-xl font-semibold text-white">จุดเด่น</h2>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                @foreach ($temple->highlights as $highlight)
                                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                        <h3 class="font-medium text-white">{{ $highlight->title }}</h3>
                                        <p class="mt-2 text-sm leading-6 text-slate-400">
                                            {{ $highlight->description ?: '-' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($galleryUsages->isNotEmpty())
                        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                            <h2 class="text-xl font-semibold text-white">Gallery</h2>

                            <div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-3">
                                @foreach ($galleryUsages as $usage)
                                    @if ($usage->media?->path)
                                        @php
                                            $galleryUrl = filter_var($usage->media->path, FILTER_VALIDATE_URL)
                                                ? $usage->media->path
                                                : \Illuminate\Support\Facades\Storage::url($usage->media->path);
                                        @endphp

                                        <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40">
                                            <img
                                                src="{{ $galleryUrl }}"
                                                alt="{{ $usage->media->title ?: $usage->media->original_filename }}"
                                                class="aspect-square w-full object-cover"
                                            >
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($temple->visitRules->isNotEmpty())
                        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                            <h2 class="text-xl font-semibold text-white">กฎการเข้าชม</h2>

                            <ul class="mt-5 space-y-3">
                                @foreach ($temple->visitRules as $rule)
                                    <li class="flex gap-3 text-sm text-slate-300">
                                        <span class="text-blue-300">•</span>
                                        <span>{{ $rule->rule_text }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-6">
                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h2 class="text-lg font-semibold text-white">ข้อมูลวัด</h2>

                        <div class="mt-5 divide-y divide-white/10 text-sm">
                            <div class="py-3">
                                <p class="text-xs text-slate-500">จังหวัด</p>
                                <p class="mt-1 text-slate-300">{{ $address?->province ?? '-' }}</p>
                            </div>

                            <div class="py-3">
                                <p class="text-xs text-slate-500">เขต / อำเภอ</p>
                                <p class="mt-1 text-slate-300">{{ $address?->district ?? '-' }}</p>
                            </div>

                            <div class="py-3">
                                <p class="text-xs text-slate-500">แขวง / ตำบล</p>
                                <p class="mt-1 text-slate-300">{{ $address?->subdistrict ?? '-' }}</p>
                            </div>

                            <div class="py-3">
                                <p class="text-xs text-slate-500">ประเภทวัด</p>
                                <p class="mt-1 text-slate-300">{{ $temple->temple_type ?: '-' }}</p>
                            </div>

                            <div class="py-3">
                                <p class="text-xs text-slate-500">นิกาย</p>
                                <p class="mt-1 text-slate-300">{{ $temple->sect ?: '-' }}</p>
                            </div>

                            <div class="py-3">
                                <p class="text-xs text-slate-500">การแต่งกาย</p>
                                <p class="mt-1 text-slate-300">{{ $temple->dress_code ?: '-' }}</p>
                            </div>
                        </div>
                    </section>

                    @if ($temple->openingHours->isNotEmpty())
                        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                            <h2 class="text-lg font-semibold text-white">เวลาเปิด-ปิด</h2>

                            @php
                                $days = [
                                    0 => 'อาทิตย์',
                                    1 => 'จันทร์',
                                    2 => 'อังคาร',
                                    3 => 'พุธ',
                                    4 => 'พฤหัสบดี',
                                    5 => 'ศุกร์',
                                    6 => 'เสาร์',
                                ];

                                $openingGroups = $temple->openingHours
                                    ->groupBy(function ($hour) {
                                        if ($hour->is_closed) {
                                            return 'closed';
                                        }

                                        $openTime = $hour->open_time
                                            ? \Carbon\Carbon::parse($hour->open_time)->format('H:i')
                                            : '--:--';

                                        $closeTime = $hour->close_time
                                            ? \Carbon\Carbon::parse($hour->close_time)->format('H:i')
                                            : '--:--';

                                        return $openTime . '-' . $closeTime;
                                    });
                            @endphp

                            <div class="mt-5 space-y-3">
                                @foreach ($openingGroups as $timeKey => $hours)
                                    @php
                                        $dayLabels = $hours
                                            ->map(fn ($hour) => $days[$hour->day_of_week] ?? null)
                                            ->filter()
                                            ->values();

                                        $dayText = $dayLabels->count() === 7
                                            ? 'ทุกวัน'
                                            : $dayLabels->join(', ');

                                        [$openTime, $closeTime] = $timeKey !== 'closed'
                                            ? explode('-', $timeKey)
                                            : [null, null];
                                    @endphp

                                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                        <div class="flex items-center justify-between gap-4 text-sm">
                                            <span class="text-slate-300">
                                                {{ $dayText }}
                                            </span>

                                            @if ($timeKey === 'closed')
                                                <span class="font-medium text-rose-300">ปิด</span>
                                            @else
                                                <span class="font-medium text-slate-100">
                                                    {{ $openTime }} - {{ $closeTime }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($temple->fees->isNotEmpty())
                        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                            <h2 class="text-lg font-semibold text-white">ค่าธรรมเนียม</h2>

                            <div class="mt-5 space-y-3">
                                @foreach ($temple->fees as $fee)
                                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                        <p class="text-sm font-medium text-white">{{ $fee->label ?: '-' }}</p>
                                        <p class="mt-1 text-sm text-slate-400">
                                            {{ $fee->amount !== null ? number_format($fee->amount, 0) . ' ' . ($fee->currency ?: 'THB') : 'ฟรี' }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($address?->google_maps_url)
                        <a
                            href="{{ $address->google_maps_url }}"
                            target="_blank"
                            rel="noopener"
                            class="block rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-center text-sm font-medium text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                        >
                            เปิดใน Google Maps
                        </a>
                    @endif
                </aside>
            </div>
        </section>
    </main>
</body>
</html>