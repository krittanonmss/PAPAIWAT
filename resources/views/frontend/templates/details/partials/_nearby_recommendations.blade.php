@php
    /** @var \Illuminate\Support\Collection|null $nearbyRecommendations */
    $nearbyRecommendations = $nearbyRecommendations ?? collect();
    $nearbyRecommendationLabels = $nearbyRecommendationLabels ?? [];
    $theme = $theme ?? 'dark';
    $isDark = $theme === 'dark';
    $sectionClass = $isDark
        ? 'overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-white/[0.07] via-white/[0.04] to-blue-500/[0.06] shadow-xl shadow-slate-950/30 backdrop-blur'
        : 'overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm';
    $cardClass = $isDark
        ? 'group rounded-2xl border border-white/10 bg-slate-950/45 p-4 transition hover:-translate-y-0.5 hover:border-blue-300/40 hover:bg-slate-950/70'
        : 'group rounded-2xl border border-stone-200 bg-stone-50 p-4 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:bg-white';
    $mutedClass = $isDark ? 'text-slate-400' : 'text-stone-600';
    $subtleClass = $isDark ? 'text-slate-500' : 'text-stone-500';
    $headingClass = $isDark ? 'text-white' : 'text-stone-950';
    $totalPlaces = $nearbyRecommendations->flatten(1)->count();
    $accentClass = $isDark ? 'text-blue-200' : 'text-emerald-700';
    $pillClass = $isDark
        ? 'border-white/10 bg-white/[0.06] text-slate-300'
        : 'border-stone-200 bg-stone-50 text-stone-600';
@endphp

@if ($nearbyRecommendations->isNotEmpty())
    <section class="{{ $sectionClass }}">
        <div class="border-b {{ $isDark ? 'border-white/10 bg-slate-950/20' : 'border-stone-200 bg-stone-50' }} p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] {{ $accentClass }}">Nearby Guide</p>
                    <h2 class="mt-2 text-2xl font-semibold {{ $headingClass }}">วางแผนต่อหลังไหว้พระ</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 {{ $mutedClass }}">
                        รวมร้านอาหาร คาเฟ่ ที่พัก และสถานที่เที่ยวใกล้วัด พร้อมระยะทางและคะแนนรีวิวแบบย่อ
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full border px-3 py-1 text-xs font-medium {{ $pillClass }}">
                        {{ number_format($totalPlaces) }} สถานที่
                    </span>
                    <span class="rounded-full border px-3 py-1 text-xs font-medium {{ $pillClass }}">
                        อัปเดตเป็นรอบ
                    </span>
                </div>
            </div>
        </div>

        <div class="space-y-6 p-6">
            @foreach ($nearbyRecommendations as $category => $items)
                <div>
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-base font-semibold {{ $headingClass }}">
                            {{ $nearbyRecommendationLabels[$category] ?? $category }}
                        </h3>
                        <span class="text-xs {{ $subtleClass }}">{{ number_format($items->count()) }} รายการ</span>
                    </div>

                    <div class="mt-3 grid gap-3 lg:grid-cols-2">
                        @foreach ($items as $place)
                            <article class="{{ $cardClass }}">
                                <div class="min-w-0">
                                    <h4 class="line-clamp-2 font-medium leading-6 {{ $headingClass }}">{{ $place->name }}</h4>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        @if ($place->rating)
                                            <span class="rounded-full border px-2.5 py-1 {{ $isDark ? 'border-amber-300/20 bg-amber-400/10 text-amber-200' : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                                                {{ number_format((float) $place->rating, 1) }} คะแนน
                                            </span>
                                        @endif
                                        @if ($place->user_ratings_total)
                                            <span class="rounded-full border px-2.5 py-1 {{ $pillClass }}">
                                                {{ number_format($place->user_ratings_total) }} รีวิว
                                            </span>
                                        @endif
                                        @if ($place->distance_label)
                                            <span class="rounded-full border px-2.5 py-1 {{ $pillClass }}">
                                                {{ $place->distance_label }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if ($place->maps_url)
                                    <a
                                        href="{{ $place->maps_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="mt-4 inline-flex items-center gap-2 text-sm font-semibold {{ $isDark ? 'text-blue-200 hover:text-blue-100' : 'text-emerald-700 hover:text-emerald-600' }}"
                                    >
                                        ดูบน Google Maps
                                        <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
                                    </a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <p class="border-t {{ $isDark ? 'border-white/10' : 'border-stone-200' }} px-6 py-4 text-xs {{ $subtleClass }}">
            Place data © Google Maps
        </p>
    </section>
@endif
