@php
    $content = $section->content_data ?? [];
    $settings = $section->settings_data ?? [];
    $summaryStats = $section->summary_stats ?? [];
    $showSearchBox = (bool) ($settings['show_search_box'] ?? false);
    $showSummaryStats = (bool) ($settings['show_summary_stats'] ?? false);
    $variant = $settings['bento_variant'] ?? 'travel';
    $backgroundClass = match ($settings['background'] ?? 'dark') {
        'plain' => 'bg-white text-slate-950',
        'soft' => 'bg-slate-100 text-slate-950',
        default => 'bg-slate-950 text-white',
    };
    $mutedTextClass = in_array(($settings['background'] ?? 'dark'), ['plain', 'soft'], true) ? 'text-slate-600' : 'text-slate-400';
    $headingClass = in_array(($settings['background'] ?? 'dark'), ['plain', 'soft'], true) ? 'text-slate-950' : 'text-white';
    $eyebrowClass = match ($variant) {
        'calm' => 'text-emerald-500',
        'editorial' => 'text-amber-300',
        default => 'text-sky-300',
    };
    $cardTintClass = match ($variant) {
        'calm' => 'from-emerald-950/55 via-slate-950/10 to-slate-950/40',
        'editorial' => 'from-slate-950/65 via-slate-950/10 to-amber-950/35',
        default => 'from-slate-950/60 via-slate-950/10 to-sky-950/35',
    };
    $items = collect($section->bento_items ?? []);
    $sizeClasses = [
        'large' => 'lg:col-span-2 lg:row-span-2 min-h-[360px]',
        'wide' => 'lg:col-span-2 min-h-[260px]',
        'tall' => 'lg:row-span-2 min-h-[360px]',
        'small' => 'min-h-[260px]',
    ];
@endphp

<section class="{{ $backgroundClass }} px-4 py-16 sm:py-20">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-end">
            <div>
                @if(!empty($content['eyebrow']))
                    <p class="text-sm font-semibold {{ $eyebrowClass }}">{{ $content['eyebrow'] }}</p>
                @endif
                <h2 class="mt-3 max-w-3xl text-3xl font-bold tracking-normal {{ $headingClass }} sm:text-4xl">{{ $content['title'] ?? 'Travel discovery' }}</h2>
            </div>
            <div class="lg:justify-self-end">
                @if(!empty($content['subtitle']))
                    <p class="max-w-xl text-sm leading-7 {{ $mutedTextClass }}">{{ $content['subtitle'] }}</p>
                @endif
                @if(!empty($content['body']))
                    <p class="mt-3 max-w-xl text-sm leading-7 {{ $mutedTextClass }}">{{ $content['body'] }}</p>
                @endif
                @if((!empty($content['primary_label']) && !empty($content['primary_url'])) || (!empty($content['secondary_label']) && !empty($content['secondary_url'])))
                    <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2">
                        @if(!empty($content['primary_label']) && !empty($content['primary_url']))
                            <a href="{{ $content['primary_url'] }}" class="text-sm font-semibold transition hover:opacity-75 {{ $headingClass }}">
                                {{ $content['primary_label'] }}
                            </a>
                        @endif
                        @if(!empty($content['secondary_label']) && !empty($content['secondary_url']))
                            <a href="{{ $content['secondary_url'] }}" class="text-sm font-semibold transition hover:opacity-75 {{ $headingClass }}">
                                {{ $content['secondary_label'] }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if($showSearchBox || $showSummaryStats)
            <div class="mt-10 grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)] lg:items-stretch">
                @if($showSearchBox)
                    <form action="{{ url('/temple-list') }}" method="GET" class="flex min-h-24 items-center rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                        <label class="sr-only" for="travel-bento-search-{{ $section->id }}">ค้นหาวัด</label>
                        <div class="flex w-full flex-col gap-3 sm:flex-row">
                            <input
                                id="travel-bento-search-{{ $section->id }}"
                                type="search"
                                name="search"
                                placeholder="ค้นหาวัด จังหวัด หรือบรรยากาศที่อยากไป..."
                                class="min-h-12 flex-1 rounded-xl border border-white/10 bg-slate-950/50 px-4 text-sm {{ $headingClass }} placeholder:text-slate-500 focus:border-white/30 focus:outline-none"
                            >
                            <button type="submit" class="min-h-12 px-1 text-sm font-semibold {{ $headingClass }} transition hover:opacity-75">ค้นหา</button>
                        </div>
                    </form>
                @endif

                @if($showSummaryStats)
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs {{ $mutedTextClass }}">วัดทั้งหมด</p>
                            <p class="mt-2 text-2xl font-bold {{ $headingClass }}">{{ number_format((int) ($summaryStats['temples'] ?? 0)) }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs {{ $mutedTextClass }}">บทความทั้งหมด</p>
                            <p class="mt-2 text-2xl font-bold {{ $headingClass }}">{{ number_format((int) ($summaryStats['articles'] ?? 0)) }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs {{ $mutedTextClass }}">ยอดผู้เข้าชม</p>
                            <p class="mt-2 text-2xl font-bold {{ $headingClass }}">{{ number_format((int) ($summaryStats['views'] ?? 0)) }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-10 grid auto-rows-[minmax(260px,auto)] gap-4 md:grid-cols-2 lg:grid-cols-4">
            @forelse($items as $item)
                @php
                    $cardClass = $sizeClasses[$item['size']] ?? $sizeClasses['small'];
                    $tag = $item['label'] ?: $item['kicker'];
                    $href = $item['url'] ?: '#';
                @endphp
                <article class="group relative overflow-hidden rounded-2xl border border-white/10 bg-slate-900 shadow-xl shadow-slate-950/20 {{ $cardClass }}">
                    <a href="{{ $href }}" class="block h-full">
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-slate-900 to-emerald-900"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-br {{ $cardTintClass }}"></div>
                        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-black/90 via-black/55 to-transparent"></div>
                        <div class="relative flex h-full flex-col justify-between p-5 sm:p-6">
                            <div class="flex items-start">
                                @if($tag)
                                    <span class="rounded-full border border-white/20 bg-white/15 px-3 py-1 text-[11px] font-medium text-white backdrop-blur">{{ $tag }}</span>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white sm:text-[1.35rem]">{{ $item['title'] }}</h3>
                                @if($item['description'])
                                    <p class="mt-2 max-w-md text-xs leading-5 text-white/82 sm:text-[13px]">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                </article>
            @empty
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-8 text-center {{ $mutedTextClass }} md:col-span-2 lg:col-span-4">ยังไม่มีรายการสำหรับ Travel Discovery Bento</div>
            @endforelse
        </div>

        @if(!empty($content['bento_note_label']) || !empty($content['bento_note_text']))
            <div class="mt-6 flex flex-col gap-2 rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                @if(!empty($content['bento_note_label']))
                    <p class="text-sm font-semibold {{ $headingClass }}">{{ $content['bento_note_label'] }}</p>
                @endif
                @if(!empty($content['bento_note_text']))
                    <p class="text-sm leading-6 {{ $mutedTextClass }}">{{ $content['bento_note_text'] }}</p>
                @endif
            </div>
        @endif
    </div>
</section>
