@php
    $frontendMenuItems = collect($frontendMenuItems ?? []);
@endphp

<nav aria-label="Main navigation">
    @if ($frontendMenuItems->isNotEmpty())
        <ul class="flex items-center gap-5 text-sm text-slate-300">
            @foreach ($frontendMenuItems as $item)
                @php
                    $children = collect($item->children ?? []);
                    $isActive = (bool) ($item->is_active ?? false) || (bool) ($item->has_active_child ?? false);
                    $target = $item->target ?: '_self';
                    $rel = $item->rel ?: ($target === '_blank' ? 'noopener noreferrer' : null);
                @endphp

                <li class="group relative">
                    <a
                        href="{{ $item->url ?? '#' }}"
                        target="{{ $target }}"
                        @if ($rel) rel="{{ $rel }}" @endif
                        class="{{ $isActive ? 'text-white' : 'hover:text-white' }} inline-flex items-center gap-1.5 transition"
                    >
                        {{ $item->label }}
                        @if ($children->isNotEmpty())
                            <span class="text-[10px] text-slate-500">▾</span>
                        @endif
                    </a>

                    @if ($children->isNotEmpty())
                        <div class="invisible absolute left-0 top-full z-50 min-w-48 translate-y-2 pt-3 opacity-0 transition group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                            <ul class="rounded-2xl border border-white/10 bg-slate-950/95 p-2 shadow-2xl shadow-slate-950/50 backdrop-blur">
                                @foreach ($children as $child)
                                    @php
                                        $childTarget = $child->target ?: '_self';
                                        $childRel = $child->rel ?: ($childTarget === '_blank' ? 'noopener noreferrer' : null);
                                    @endphp

                                    <li>
                                        <a
                                            href="{{ $child->url ?? '#' }}"
                                            target="{{ $childTarget }}"
                                            @if ($childRel) rel="{{ $childRel }}" @endif
                                            class="{{ $child->is_active ?? false ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} block rounded-xl px-3 py-2 transition"
                                        >
                                            {{ $child->label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <ul class="flex items-center gap-5 text-sm text-slate-300">
            <li><a href="{{ route('home') }}" class="hover:text-white">หน้าแรก</a></li>
        </ul>
    @endif
</nav>
