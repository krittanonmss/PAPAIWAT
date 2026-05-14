@php
    $content = $section->content_data ?? [];
    $settings = $section->settings_data ?? [];
    $showSearchBox = (bool) ($settings['show_search_box'] ?? false);
    $variant = $settings['bento_variant'] ?? 'travel';
    $isFilteredBento = $variant === 'article_filter';
    $bentoContentType = ($settings['bento_content_type'] ?? 'article') === 'temple' ? 'temple' : 'article';
    $showPrimaryButton = (bool) ($content['primary_enabled'] ?? true);
    $showSecondaryButton = (bool) ($content['secondary_enabled'] ?? true);
    $hasBentoButtons = ($showPrimaryButton && !empty($content['primary_label']) && !empty($content['primary_url']))
        || ($showSecondaryButton && !empty($content['secondary_label']) && !empty($content['secondary_url']));
    $filters = $section->filters ?? [];
    $categories = collect($filters['categories'] ?? []);
    $contentTypeLabel = $bentoContentType === 'temple' ? 'วัด' : 'บทความ';
    $searchPlaceholder = ($content['search_placeholder'] ?? '') ?: 'ค้นหา' . $contentTypeLabel . '...';
    $categoryLabel = ($content['category_all_label'] ?? '') ?: 'ทุกสาย' . $contentTypeLabel;
    $contentAlign = in_array(($settings['bento_content_align'] ?? 'left'), ['left', 'center', 'right'], true)
        ? $settings['bento_content_align']
        : 'left';
    $blockBoxStyle = match ($contentAlign) {
        'center' => 'margin-left: auto; margin-right: auto; text-align: center;',
        'right' => 'margin-left: auto; margin-right: 0; text-align: right;',
        default => 'margin-left: 0; margin-right: auto; text-align: left;',
    };
    $buttonClass = 'inline-flex rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white';
    $mutedTextClass = 'text-slate-400';
    $headingClass = 'text-white';
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
    $itemCount = min($items->count(), 12);
    $layoutAreas = [
        1 => ['"a a a a"'],
        2 => ['"a a b b"'],
        3 => ['"a a b b"', '"a a c c"'],
        4 => ['"a a b b"', '"c c d d"'],
        5 => ['"a a b c"', '"a a d e"'],
        6 => ['"a a b b"', '"c c d d"', '"e e f f"'],
        7 => ['"a a b c"', '"a a d e"', '"f f g g"'],
        8 => ['"a a b b"', '"c c d d"', '"e e f f"', '"g g h h"'],
        9 => ['"a a b c"', '"a a d e"', '"f g h i"'],
        10 => ['"a a b b"', '"c c d d"', '"e e f f"', '"g g h h"', '"i i j j"'],
        11 => ['"a a b c"', '"a a d e"', '"f g h i"', '"j j k k"'],
        12 => ['"a b c d"', '"e f g h"', '"i j k l"'],
    ];
    $areaNames = range('a', 'l');
    $gridId = 'travel-bento-grid-' . ($section->id ?: 'preview');
    $sectionFilterId = 'section-filter-' . ($section->id ?: 'preview');
    $gridTemplateAreas = implode(' ', $layoutAreas[$itemCount] ?? $layoutAreas[12]);
@endphp
@if($items->isNotEmpty())
    <style>
        @media (min-width: 1024px) {
            #{{ $gridId }} {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                grid-template-areas: {!! $gridTemplateAreas !!};
                grid-auto-rows: minmax({{ $isFilteredBento ? '220px' : '260px' }}, auto);
            }
            @foreach($areaNames as $index => $areaName)
                #{{ $gridId }} > :nth-child({{ $index + 1 }}) { grid-area: {{ $areaName }}; }
            @endforeach
        }
    </style>
@endif
<section id="{{ $sectionFilterId }}" class="px-4 py-16 text-white sm:py-20" data-section-filter-root style="@include('frontend.templates.sections._background')">
    <div class="mx-auto max-w-7xl">
        <div class="{{ $contentAlign === 'left' && $hasBentoButtons ? 'grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end' : '' }}">
            <div class="max-w-3xl" style="{{ $blockBoxStyle }}">
                @if(!empty($content['eyebrow']))
                    <p class="text-sm font-semibold {{ $eyebrowClass }}">{{ $content['eyebrow'] }}</p>
                @endif
                <h2 class="mt-3 text-3xl font-bold tracking-normal {{ $headingClass }} sm:text-4xl">{{ $content['title'] ?? 'Travel discovery' }}</h2>
                @if(!empty($content['subtitle']))
                    <p class="mt-4 text-sm leading-7 {{ $mutedTextClass }}">{{ $content['subtitle'] }}</p>
                @endif
                @if(!empty($content['body']))
                    <p class="mt-3 text-sm leading-7 {{ $mutedTextClass }}">{{ $content['body'] }}</p>
                @endif
            </div>

            @if($contentAlign === 'left' && $hasBentoButtons)
                <div class="flex flex-wrap gap-3 lg:justify-end">
                    @if($showPrimaryButton && !empty($content['primary_label']) && !empty($content['primary_url']))
                        <a href="{{ $content['primary_url'] }}" class="{{ $buttonClass }}">
                            {{ $content['primary_label'] }}
                        </a>
                    @endif
                    @if($showSecondaryButton && !empty($content['secondary_label']) && !empty($content['secondary_url']))
                        <a href="{{ $content['secondary_url'] }}" class="{{ $buttonClass }}">
                            {{ $content['secondary_label'] }}
                        </a>
                    @endif
                </div>
            @endif
        </div>

        @if($isFilteredBento)
            <form action="{{ url()->current() }}#{{ $sectionFilterId }}" method="GET" class="mx-auto mt-10 grid max-w-4xl gap-3 rounded-2xl border border-white/10 bg-white/[0.04] p-4 sm:grid-cols-[minmax(0,1fr)_minmax(0,0.9fr)_auto] sm:items-center" data-section-filter-form>
                <label class="sr-only" for="content-bento-search-{{ $section->id }}">ค้นหา{{ $contentTypeLabel }}</label>
                <input
                    id="content-bento-search-{{ $section->id }}"
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="min-h-12 rounded-xl border border-white/10 bg-slate-950/50 px-4 text-sm {{ $headingClass }} placeholder:text-slate-500 focus:border-white/30 focus:outline-none"
                >
                <select name="category" class="min-h-12 rounded-xl border border-white/10 bg-slate-950/50 px-4 text-sm {{ $headingClass }} focus:border-white/30 focus:outline-none">
                    <option value="">{{ $categoryLabel }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="min-h-12 rounded-xl border border-white/10 bg-white/[0.08] px-5 text-sm font-semibold {{ $headingClass }} transition hover:bg-white/15">{{ ($content['submit_label'] ?? '') ?: 'กรอง' }}</button>
            </form>
        @elseif($showSearchBox)
            <div class="mx-auto mt-10 max-w-4xl">
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
                        <button type="submit" class="min-h-12 rounded-xl border border-white/10 bg-white/[0.08] px-5 text-sm font-semibold {{ $headingClass }} transition hover:bg-white/15">ค้นหา</button>
                    </div>
                </form>
            </div>
        @endif

        <div class="mx-auto mt-10 max-w-7xl rounded-3xl border border-white/10 bg-white/[0.035] p-3 shadow-2xl shadow-slate-950/25 sm:p-4">
            <div id="{{ $gridId }}" class="grid gap-4 md:grid-cols-2">
                @forelse($items as $item)
                    @php
                        $tag = $item['label'] ?: $item['kicker'];
                        $href = $item['url'] ?: '#';
                    @endphp
                    <article class="group relative min-h-[220px] overflow-hidden rounded-2xl border border-white/10 bg-slate-900 shadow-xl shadow-slate-950/20">
                        <a href="{{ $href }}" class="block h-full">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-slate-900 to-emerald-900"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-br {{ $cardTintClass }}"></div>
                            <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-black/90 via-black/55 to-transparent"></div>
                            <div class="relative flex h-full flex-col justify-between p-5 sm:p-6">
                                <div class="flex flex-wrap items-start gap-2">
                                    @if($tag)
                                        <span class="rounded-full border border-white/20 bg-white/15 px-3 py-1 text-[11px] font-medium text-white backdrop-blur">{{ $tag }}</span>
                                    @endif
                                    @if($isFilteredBento && !empty($item['kicker']))
                                        <span class="rounded-full border border-white/10 bg-black/25 px-3 py-1 text-[11px] text-white/80 backdrop-blur">{{ $item['kicker'] }}</span>
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
                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-8 text-center {{ $mutedTextClass }} md:col-span-2 lg:col-span-4">ยังไม่มีรายการสำหรับ Bento</div>
                @endforelse
            </div>
        </div>

        @if($contentAlign !== 'left' && $hasBentoButtons)
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                @if($showPrimaryButton && !empty($content['primary_label']) && !empty($content['primary_url']))
                    <a href="{{ $content['primary_url'] }}" class="{{ $buttonClass }}">
                        {{ $content['primary_label'] }}
                    </a>
                @endif
                @if($showSecondaryButton && !empty($content['secondary_label']) && !empty($content['secondary_url']))
                    <a href="{{ $content['secondary_url'] }}" class="{{ $buttonClass }}">
                        {{ $content['secondary_label'] }}
                    </a>
                @endif
            </div>
        @endif

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
