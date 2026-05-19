@php
    $frontendFooterMenuItems = collect($frontendFooterMenuItems ?? []);
    $frontendFooterSettings = \App\Support\FooterSettings::normalize($frontendFooterSettings ?? []);
    $footerMenuColumns = $frontendFooterMenuItems
        ->filter(fn ($item) => collect($item->children ?? [])->isNotEmpty())
        ->values();
    $footerStandaloneItems = $frontendFooterMenuItems
        ->filter(fn ($item) => collect($item->children ?? [])->isEmpty())
        ->values();
    $footerShellClass = match ($frontendFooterSettings['background_style']) {
        'solid' => 'mt-16 bg-slate-950',
        'minimal' => 'mt-16 bg-transparent',
        default => 'mt-16 bg-slate-950/95 backdrop-blur',
    };
    $footerBorderClass = $frontendFooterSettings['show_border'] ? 'border-t border-white/10' : '';
    $footerGridClass = match ($frontendFooterSettings['column_count']) {
        '3' => 'grid gap-8 md:grid-cols-3',
        '5' => 'grid gap-8 md:grid-cols-3 lg:grid-cols-5',
        default => 'grid gap-8 md:grid-cols-3 lg:grid-cols-4',
    };
    $footerCopyright = \App\Support\FooterSettings::copyright($frontendFooterSettings);
@endphp

<footer class="{{ $footerShellClass }} {{ $footerBorderClass }}">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:py-14">
        <div class="{{ $footerGridClass }} items-start">
            @if($frontendFooterSettings['show_brand'])
                <div class="md:col-span-1 lg:pr-6">
                    @if($frontendFooterSettings['brand_title'])
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-white">
                            <span class="grid h-11 w-11 place-items-center rounded-2xl border border-white/10 bg-white/[0.06] text-sm font-bold shadow-lg shadow-slate-950/30">
                                {{ mb_substr($frontendFooterSettings['brand_title'], 0, 1) }}
                            </span>
                            <span class="text-lg font-semibold tracking-normal">{{ $frontendFooterSettings['brand_title'] }}</span>
                        </a>
                    @endif

                    @if($frontendFooterSettings['brand_description'])
                        <p class="mt-4 max-w-sm text-sm leading-7 text-slate-400">
                            {{ $frontendFooterSettings['brand_description'] }}
                        </p>
                    @endif

                    @if($frontendFooterSettings['footer_note'])
                        <p class="mt-5 rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-xs leading-5 text-slate-400">
                            {{ $frontendFooterSettings['footer_note'] }}
                        </p>
                    @endif
                </div>
            @endif

            @if($frontendFooterSettings['show_menu'] && $frontendFooterMenuItems->isNotEmpty())
                @if($footerStandaloneItems->isNotEmpty())
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">เมนู</h4>
                        <ul class="mt-4 space-y-2.5 text-sm text-slate-400">
                            @foreach($footerStandaloneItems as $item)
                                @php
                                    $target = $item->target ?: '_self';
                                    $rel = $item->rel ?: ($target === '_blank' ? 'noopener noreferrer' : null);
                                    $isActive = (bool) ($item->is_active ?? false);
                                    $isTextOnly = ($item->menu_item_type ?? null) === 'heading';
                                @endphp

                                <li>
                                    @if($isTextOnly)
                                        <span class="text-slate-300">
                                            {{ $item->label }}
                                        </span>
                                    @else
                                        <a
                                            href="{{ $item->url ?? '#' }}"
                                            target="{{ $target }}"
                                            @if ($rel) rel="{{ $rel }}" @endif
                                            class="{{ $isActive ? 'text-white' : 'hover:text-white' }} inline-flex items-center gap-2 transition"
                                        >
                                            <span class="h-px w-3 bg-current opacity-40"></span>
                                            {{ $item->label }}
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @foreach($footerMenuColumns as $item)
                    @php
                        $children = collect($item->children ?? []);
                        $target = $item->target ?: '_self';
                        $rel = $item->rel ?: ($target === '_blank' ? 'noopener noreferrer' : null);
                        $isHeadingLink = ($item->menu_item_type ?? null) !== 'heading'
                            && !empty($item->url)
                            && $item->url !== '#';
                        $isActive = (bool) ($item->is_active ?? false) || (bool) ($item->has_active_child ?? false);
                    @endphp

                    <div>
                        @if($isHeadingLink)
                            <a
                                href="{{ $item->url }}"
                                target="{{ $target }}"
                                @if ($rel) rel="{{ $rel }}" @endif
                                class="{{ $isActive ? 'text-white' : 'text-slate-200 hover:text-white' }} text-xs font-semibold uppercase tracking-[0.16em] transition"
                            >
                                {{ $item->label }}
                            </a>
                        @else
                            <h4 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $item->label }}</h4>
                        @endif

                        <ul class="mt-4 space-y-2.5 text-sm text-slate-400">
                            @foreach($children as $child)
                                @php
                                    $childTarget = $child->target ?: '_self';
                                    $childRel = $child->rel ?: ($childTarget === '_blank' ? 'noopener noreferrer' : null);
                                    $childIsActive = (bool) ($child->is_active ?? false) || (bool) ($child->has_active_child ?? false);
                                    $grandchildren = collect($child->children ?? []);
                                @endphp

                                <li>
                                    <a
                                        href="{{ $child->url ?? '#' }}"
                                        target="{{ $childTarget }}"
                                        @if ($childRel) rel="{{ $childRel }}" @endif
                                        class="{{ $childIsActive ? 'text-white' : 'hover:text-white' }} inline-flex items-center gap-2 transition"
                                    >
                                        <span class="h-px w-3 bg-current opacity-40"></span>
                                        {{ $child->label }}
                                    </a>

                                    @if($grandchildren->isNotEmpty())
                                        <ul class="mt-2 space-y-1.5 border-l border-white/10 pl-4 text-xs text-slate-500">
                                            @foreach($grandchildren->take(6) as $grandchild)
                                                @php
                                                    $grandchildTarget = $grandchild->target ?: '_self';
                                                    $grandchildRel = $grandchild->rel ?: ($grandchildTarget === '_blank' ? 'noopener noreferrer' : null);
                                                    $grandchildIsActive = (bool) ($grandchild->is_active ?? false);
                                                @endphp
                                                <li>
                                                    <a
                                                        href="{{ $grandchild->url ?? '#' }}"
                                                        target="{{ $grandchildTarget }}"
                                                        @if ($grandchildRel) rel="{{ $grandchildRel }}" @endif
                                                        class="{{ $grandchildIsActive ? 'text-white' : 'hover:text-slate-200' }} transition"
                                                    >
                                                        {{ $grandchild->label }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @elseif($frontendFooterSettings['show_menu'])
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">เมนู</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white">หน้าแรก</a></li>
                        <li><a href="{{ url('/temple-list') }}" class="hover:text-white">วัด</a></li>
                        <li><a href="{{ url('/article-list') }}" class="hover:text-white">บทความ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">ข้อมูล</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ url('/about') }}" class="hover:text-white">เกี่ยวกับเรา</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:text-white">ติดต่อ</a></li>
                    </ul>
                </div>
            @endif
        </div>

        @if($frontendFooterSettings['show_bottom_bar'])
            <div class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-6 text-xs text-slate-500 md:flex-row md:items-center md:justify-between">
                <p>{{ $footerCopyright }}</p>

                @if($frontendFooterSettings['show_menu'] && $footerStandaloneItems->isNotEmpty())
                    <div class="flex flex-wrap gap-x-4 gap-y-2">
                        @foreach($footerStandaloneItems->take(4) as $item)
                            @continue(($item->menu_item_type ?? null) === 'heading')
                            @php
                                $target = $item->target ?: '_self';
                                $rel = $item->rel ?: ($target === '_blank' ? 'noopener noreferrer' : null);
                            @endphp
                            <a
                                href="{{ $item->url ?? '#' }}"
                                target="{{ $target }}"
                                @if ($rel) rel="{{ $rel }}" @endif
                                class="transition hover:text-slate-300"
                            >
                                {{ $item->label }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</footer>
