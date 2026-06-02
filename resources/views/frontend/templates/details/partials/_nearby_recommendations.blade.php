@php
    /** @var \Illuminate\Support\Collection|null $nearbyRecommendations */
    $nearbyRecommendations = $nearbyRecommendations ?? collect();
    $nearbyRecommendationLabels = $nearbyRecommendationLabels ?? [];
    $theme = $theme ?? 'dark';
    $isDark = $theme === 'dark';
    $sectionClass = $isDark
        ? 'overflow-hidden rounded-3xl border border-white/10 bg-slate-950/35 shadow-xl shadow-slate-950/30 backdrop-blur'
        : 'overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm';
    $cardClass = $isDark
        ? 'group flex w-[15.5rem] shrink-0 snap-start flex-col overflow-hidden rounded-2xl border border-white/10 bg-slate-950/55 transition hover:-translate-y-0.5 hover:border-blue-300/40 hover:bg-slate-950/75 sm:w-[17rem]'
        : 'group flex w-[15.5rem] shrink-0 snap-start flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md sm:w-[17rem]';
    $mutedClass = $isDark ? 'text-slate-400' : 'text-stone-600';
    $subtleClass = $isDark ? 'text-slate-500' : 'text-stone-500';
    $headingClass = $isDark ? 'text-white' : 'text-stone-950';
    $totalPlaces = $nearbyRecommendations->flatten(1)->count();
    $accentClass = $isDark ? 'text-blue-200' : 'text-emerald-700';
    $pillClass = $isDark
        ? 'border-white/10 bg-white/[0.06] text-slate-300'
        : 'border-stone-200 bg-stone-50 text-stone-600';
    $arrowClass = $isDark
        ? 'border-white/10 bg-slate-950/80 text-white hover:border-blue-300/40 hover:bg-slate-900'
        : 'border-stone-200 bg-white text-stone-900 hover:border-emerald-300 hover:bg-stone-50';
@endphp

@if ($nearbyRecommendations->isNotEmpty())
    <section class="{{ $sectionClass }}">
        <div class="border-b {{ $isDark ? 'border-white/10 bg-slate-950/20' : 'border-stone-200 bg-stone-50' }} px-5 py-5 sm:px-6">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase {{ $accentClass }}">Nearby Guide</p>
                    <h2 class="mt-2 text-2xl font-semibold {{ $headingClass }}">วางแผนต่อหลังไหว้พระ</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 {{ $mutedClass }}">
                        ร้านอาหาร คาเฟ่ ที่พัก และสถานที่เที่ยวใกล้เคียง พร้อมรูป ระยะทาง และคะแนนรีวิว
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

        <div class="space-y-6 p-5 sm:p-6">
            @foreach ($nearbyRecommendations as $category => $items)
                <div data-nearby-carousel>
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-base font-semibold {{ $headingClass }}">
                            {{ $nearbyRecommendationLabels[$category] ?? $category }}
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs {{ $subtleClass }}">{{ number_format($items->count()) }} รายการ</span>
                            @if ($items->count() > 1)
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border text-lg leading-none transition {{ $arrowClass }}"
                                    data-nearby-prev
                                    aria-label="ก่อนหน้า"
                                >
                                    ‹
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border text-lg leading-none transition {{ $arrowClass }}"
                                    data-nearby-next
                                    aria-label="ถัดไป"
                                >
                                    ›
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-nearby-track>
                        @foreach ($items as $place)
                            @php
                                $photoUrl = collect($place->photo_urls ?? [])->filter()->first();
                            @endphp
                            <article class="{{ $cardClass }}">
                                <div class="h-52 w-full overflow-hidden sm:h-56">
                                    @if ($photoUrl)
                                        <img
                                            src="{{ $photoUrl }}"
                                            alt="{{ $place->name }}"
                                            loading="lazy"
                                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center {{ $isDark ? 'bg-white/[0.04] text-slate-500' : 'bg-stone-100 text-stone-400' }}">
                                            <span class="px-3 text-center text-xs font-medium leading-5">ไม่มีรูปภาพ</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex min-h-[12.5rem] min-w-0 flex-1 flex-col p-4">
                                    <div class="min-w-0">
                                        <h4 class="line-clamp-2 text-base font-semibold leading-6 {{ $headingClass }}">{{ $place->name }}</h4>

                                        <div class="mt-2 flex flex-wrap gap-1.5 text-xs">
                                            @if ($place->rating)
                                                <span class="rounded-full border px-2 py-0.5 {{ $isDark ? 'border-amber-300/20 bg-amber-400/10 text-amber-200' : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                                                    {{ number_format((float) $place->rating, 1) }} คะแนน
                                                </span>
                                            @endif
                                            @if ($place->distance_label)
                                                <span class="rounded-full border px-2 py-0.5 {{ $pillClass }}">
                                                    {{ $place->distance_label }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($place->user_ratings_total)
                                            <p class="mt-2 text-xs {{ $subtleClass }}">
                                                {{ number_format($place->user_ratings_total) }} รีวิวบน Google
                                            </p>
                                        @endif
                                    </div>

                                    @if ($place->maps_url)
                                        <a
                                            href="{{ $place->maps_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="mt-auto inline-flex items-center gap-1.5 pt-3 text-sm font-semibold {{ $isDark ? 'text-blue-200 hover:text-blue-100' : 'text-emerald-700 hover:text-emerald-600' }}"
                                        >
                                            เปิดแผนที่
                                            <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
                                        </a>
                                    @endif
                                </div>
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

    <script>
        (() => {
            const roots = document.querySelectorAll('[data-nearby-carousel]:not([data-nearby-ready])');

            roots.forEach((root) => {
                root.dataset.nearbyReady = 'true';

                const track = root.querySelector('[data-nearby-track]');
                const previous = root.querySelector('[data-nearby-prev]');
                const next = root.querySelector('[data-nearby-next]');

                if (!track || track.children.length < 2) {
                    return;
                }

                const slide = (direction = 1) => {
                    const firstCard = track.querySelector('article');
                    const gap = 12;
                    const step = firstCard ? firstCard.getBoundingClientRect().width + gap : track.clientWidth;
                    const maxScroll = track.scrollWidth - track.clientWidth - 2;
                    const goingForwardPastEnd = direction > 0 && track.scrollLeft >= maxScroll;
                    const goingBackwardPastStart = direction < 0 && track.scrollLeft <= 2;

                    if (goingForwardPastEnd) {
                        track.scrollTo({ left: 0, behavior: 'smooth' });
                        return;
                    }

                    if (goingBackwardPastStart) {
                        track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' });
                        return;
                    }

                    track.scrollBy({ left: step * direction, behavior: 'smooth' });
                };

                let timer = window.setInterval(() => slide(1), 4500);
                const stop = () => window.clearInterval(timer);
                const start = () => {
                    stop();
                    timer = window.setInterval(() => slide(1), 4500);
                };

                previous?.addEventListener('click', () => {
                    slide(-1);
                    start();
                });
                next?.addEventListener('click', () => {
                    slide(1);
                    start();
                });
                root.addEventListener('mouseenter', stop);
                root.addEventListener('mouseleave', start);
                root.addEventListener('focusin', stop);
                root.addEventListener('focusout', start);
            });
        })();
    </script>
@endif
