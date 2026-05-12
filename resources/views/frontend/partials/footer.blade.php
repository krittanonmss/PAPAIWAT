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
        default => 'mt-16 bg-white/[0.03] backdrop-blur',
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
    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="{{ $footerGridClass }}">
            @if($frontendFooterSettings['show_brand'])
                <div>
                    @if($frontendFooterSettings['brand_title'])
                        <h3 class="text-lg font-semibold text-white">{{ $frontendFooterSettings['brand_title'] }}</h3>
                    @endif

                    @if($frontendFooterSettings['brand_description'])
                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            {{ $frontendFooterSettings['brand_description'] }}
                        </p>
                    @endif

                    @if($frontendFooterSettings['footer_note'])
                        <p class="mt-4 text-xs leading-5 text-slate-500">
                            {{ $frontendFooterSettings['footer_note'] }}
                        </p>
                    @endif
                </div>
            @endif

            @if($frontendFooterSettings['show_menu'] && $frontendFooterMenuItems->isNotEmpty())
                @if($footerStandaloneItems->isNotEmpty())
                    <div>
                        <h4 class="text-sm font-medium text-slate-200">เมนู</h4>
                        <ul class="mt-3 space-y-2 text-sm text-slate-400">
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
                                            class="{{ $isActive ? 'text-white' : 'hover:text-white' }} transition"
                                        >
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
                                class="{{ $isActive ? 'text-white' : 'text-slate-200 hover:text-white' }} text-sm font-medium transition"
                            >
                                {{ $item->label }}
                            </a>
                        @else
                            <h4 class="text-sm font-medium text-slate-200">{{ $item->label }}</h4>
                        @endif

                        <ul class="mt-3 space-y-2 text-sm text-slate-400">
                            @foreach($children as $child)
                                @php
                                    $childTarget = $child->target ?: '_self';
                                    $childRel = $child->rel ?: ($childTarget === '_blank' ? 'noopener noreferrer' : null);
                                    $childIsActive = (bool) ($child->is_active ?? false);
                                @endphp

                                <li>
                                    <a
                                        href="{{ $child->url ?? '#' }}"
                                        target="{{ $childTarget }}"
                                        @if ($childRel) rel="{{ $childRel }}" @endif
                                        class="{{ $childIsActive ? 'text-white' : 'hover:text-white' }} transition"
                                    >
                                        {{ $child->label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @elseif($frontendFooterSettings['show_menu'])
                <div>
                    <h4 class="text-sm font-medium text-slate-200">เมนู</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white">หน้าแรก</a></li>
                        <li><a href="{{ url('/temple-list') }}" class="hover:text-white">วัด</a></li>
                        <li><a href="{{ url('/article-list') }}" class="hover:text-white">บทความ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-slate-200">ข้อมูล</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-400">
                        <li><a href="{{ url('/about') }}" class="hover:text-white">เกี่ยวกับเรา</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:text-white">ติดต่อ</a></li>
                    </ul>
                </div>
            @endif
        </div>

        @if($frontendFooterSettings['show_bottom_bar'])
            <div class="mt-10 border-t border-white/10 pt-6 text-center text-xs text-slate-500">
                {{ $footerCopyright }}
            </div>
        @endif
    </div>
</footer>
