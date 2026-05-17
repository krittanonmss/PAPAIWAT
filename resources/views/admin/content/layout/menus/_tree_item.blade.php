@php
    $children = $childrenByParent->get($item->id, collect());
    $depth = (int) ($depth ?? 0);
    $url = \App\Support\MenuUrl::resolve($item);
    $typeLabel = [
        'heading' => 'Heading',
        'route' => 'Route',
        'page' => 'Page',
        'content' => 'Content',
        'external_url' => 'URL',
        'anchor' => 'Anchor',
    ][$item->menu_item_type] ?? $item->menu_item_type;
@endphp

<div class="{{ $depth > 0 ? 'ml-5 border-l border-white/10 pl-4' : '' }}">
    <div class="group rounded-2xl border border-white/10 bg-slate-950/40 p-4 transition hover:border-blue-400/30 hover:bg-white/[0.06]">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full border border-white/10 bg-white/[0.05] px-2.5 py-1 text-xs font-medium text-slate-300">
                        {{ $typeLabel }}
                    </span>
                    @if($item->is_enabled)
                        <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-300">Enabled</span>
                    @else
                        <span class="inline-flex rounded-full border border-slate-400/20 bg-slate-500/10 px-2.5 py-1 text-xs font-medium text-slate-400">Hidden</span>
                    @endif
                    @if($children->isNotEmpty())
                        <span class="text-xs text-slate-500">{{ $children->count() }} children</span>
                    @endif
                </div>
                <h3 class="mt-2 truncate text-base font-semibold text-white">{{ $item->label }}</h3>
                <p class="mt-1 truncate text-sm text-slate-400">{{ $url }}</p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <span class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-semibold text-slate-300">
                    #{{ $item->sort_order }}
                </span>
                <span class="rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-semibold text-slate-300">
                    {{ $item->target ?: '_self' }}
                </span>
                <a href="{{ route('admin.content.menu-items.edit', [$menu, $item]) }}" class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-500">
                    Edit
                </a>
            </div>
        </div>
    </div>

    @if($children->isNotEmpty())
        <div class="mt-3 space-y-3">
            @foreach($children as $child)
                @include('admin.content.layout.menus._tree_item', [
                    'menu' => $menu,
                    'item' => $child,
                    'childrenByParent' => $childrenByParent,
                    'depth' => $depth + 1,
                ])
            @endforeach
        </div>
    @endif
</div>
