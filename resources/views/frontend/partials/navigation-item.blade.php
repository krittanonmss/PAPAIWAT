@php
    use App\Support\MenuUrl;

    $label = $item->label
        ?? $item->title
        ?? $item->name
        ?? 'Menu';

    $url = MenuUrl::resolve($item);
    $target = $item->target ?? '_self';
    $rel = $item->rel ?? null;
    $hasChildren = isset($item->children) && $item->children->isNotEmpty();
@endphp

<div class="relative group">
    <a
        href="{{ $url }}"
        target="{{ $target }}"
        @if ($rel) rel="{{ $rel }}" @endif
        class="inline-flex items-center gap-1 text-slate-700 hover:text-slate-900"
    >
        {{ $label }}

        @if ($hasChildren)
            <span class="text-xs text-slate-400" aria-hidden="true">▾</span>
        @endif
    </a>

    @if ($hasChildren)
        <div class="invisible absolute left-0 top-full z-20 min-w-48 rounded-xl border border-slate-200 bg-white p-2 opacity-0 shadow-lg transition group-hover:visible group-hover:opacity-100">
            @foreach ($item->children as $child)
                @php
                    $childLabel = $child->label
                        ?? $child->title
                        ?? $child->name
                        ?? 'Menu';

                    $childUrl = MenuUrl::resolve($child);
                    $childTarget = $child->target ?? '_self';
                    $childRel = $child->rel ?? null;
                @endphp

                <a
                    href="{{ $childUrl }}"
                    target="{{ $childTarget }}"
                    @if ($childRel) rel="{{ $childRel }}" @endif
                    class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900"
                >
                    {{ $childLabel }}
                </a>
            @endforeach
        </div>
    @endif
</div>