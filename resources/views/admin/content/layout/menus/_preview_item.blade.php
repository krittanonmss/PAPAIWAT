@php
    $children = collect($item->children ?? []);
    $depth = (int) ($depth ?? 0);
@endphp

<li>
    <div class="flex items-center justify-between gap-3 rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 {{ $depth > 0 ? 'ml-4' : '' }}">
        <span class="min-w-0 truncate text-slate-100">{{ $item->label }}</span>
        <span class="shrink-0 truncate text-xs text-slate-500">{{ $item->url ?? '#' }}</span>
    </div>

    @if ($children->isNotEmpty())
        <ul class="mt-2 space-y-2">
            @foreach ($children as $child)
                @include('admin.content.layout.menus._preview_item', [
                    'item' => $child,
                    'depth' => $depth + 1,
                ])
            @endforeach
        </ul>
    @endif
</li>
