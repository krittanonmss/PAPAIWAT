@foreach ($items as $item)
    @php
        $children = collect($item->children ?? []);
        $isActive = (bool) ($item->is_active ?? false) || (bool) ($item->has_active_child ?? false);
        $target = $item->target ?: '_self';
        $rel = $item->rel ?: ($target === '_blank' ? 'noopener noreferrer' : null);
        $level = (int) ($level ?? 0);
        $hasChildren = $children->isNotEmpty();
    @endphp

    <li class="group/menu-item relative">
        <a
            href="{{ $item->url ?? '#' }}"
            target="{{ $target }}"
            @if ($rel) rel="{{ $rel }}" @endif
            class="{{ $level === 0
                ? ($isActive ? 'text-white' : 'hover:text-white') . ' inline-flex items-center gap-1.5 transition'
                : ($isActive ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white') . ' flex items-center justify-between gap-4 rounded-xl px-3 py-2 transition' }}"
        >
            <span class="min-w-0 truncate">{{ $item->label }}</span>
            @if ($hasChildren)
                <span class="{{ $level === 0 ? 'text-[10px]' : 'text-xs' }} shrink-0 text-slate-500">
                    {{ $level === 0 ? '▾' : '›' }}
                </span>
            @endif
        </a>

        @if ($hasChildren)
            <div class="{{ $level === 0
                ? 'left-0 top-full translate-y-2 pt-3 group-hover/menu-item:translate-y-0 group-focus-within/menu-item:translate-y-0'
                : 'left-full top-0 translate-x-2 pl-2 group-hover/menu-item:translate-x-0 group-focus-within/menu-item:translate-x-0' }} invisible absolute z-50 min-w-56 opacity-0 transition group-hover/menu-item:visible group-hover/menu-item:opacity-100 group-focus-within/menu-item:visible group-focus-within/menu-item:opacity-100">
                <ul class="rounded-2xl border border-white/10 bg-slate-950/95 p-2 shadow-2xl shadow-slate-950/50 backdrop-blur">
                    @include('frontend.partials._navigation_items', [
                        'items' => $children,
                        'level' => $level + 1,
                    ])
                </ul>
            </div>
        @endif
    </li>
@endforeach
