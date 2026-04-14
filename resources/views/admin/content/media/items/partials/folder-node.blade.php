@php
    $indentClass = match ($level) {
        0 => '',
        1 => 'ml-6',
        2 => 'ml-12',
        3 => 'ml-16',
        default => 'ml-20',
    };

    $directMedia = $mediaByFolderId->get($folder->id) ?? collect();

    $hasVisibleChildMedia = false;

    foreach ($folder->children as $child) {
        if (($mediaByFolderId->get($child->id) ?? collect())->isNotEmpty()) {
            $hasVisibleChildMedia = true;
            break;
        }

        foreach ($child->children as $grandChild) {
            if (($mediaByFolderId->get($grandChild->id) ?? collect())->isNotEmpty()) {
                $hasVisibleChildMedia = true;
                break 2;
            }

            foreach ($grandChild->children as $greatGrandChild) {
                if (($mediaByFolderId->get($greatGrandChild->id) ?? collect())->isNotEmpty()) {
                    $hasVisibleChildMedia = true;
                    break 3;
                }
            }
        }
    }

    $hasChildren = $directMedia->isNotEmpty() || $hasVisibleChildMedia;
@endphp

<div class="{{ $indentClass }}">
    <details class="group rounded-2xl border border-slate-200 bg-white shadow-sm" @if($level === 0) open @endif>
        <summary class="list-none cursor-pointer px-4 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7.5A2.5 2.5 0 015.5 5h4.379a2.5 2.5 0 011.768.732l.621.622A2.5 2.5 0 0014.036 7H18.5A2.5 2.5 0 0121 9.5v8A2.5 2.5 0 0118.5 20h-13A2.5 2.5 0 013 17.5v-10z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-slate-300 text-xs text-slate-500 transition group-open:rotate-90">
                                >
                            </span>

                            <h3 class="truncate text-base font-semibold text-slate-900">
                                {{ $folder->name }}
                            </h3>

                            @if ($folder->status === 'active')
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-medium text-slate-700">
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                            <span>{{ $folder->parent?->name ?? 'Root Folder' }}</span>
                            <span>/{{ $folder->slug }}</span>
                            <span>{{ $directMedia->count() }} files</span>
                        </div>
                    </div>
                </div>

                <div class="text-sm text-slate-400">
                    {{ $hasChildren ? 'Click to expand' : 'Empty folder' }}
                </div>
            </div>
        </summary>

        <div class="border-t border-slate-100 px-4 py-4">
            @if ($directMedia->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                    @foreach ($directMedia as $media)
                        @include('admin.content.media.items.partials.media-card', [
                            'media' => $media,
                        ])
                    @endforeach
                </div>
            @endif

            @php
                $visibleChildren = $folder->children->filter(function ($child) use ($mediaByFolderId) {
                    if (($mediaByFolderId->get($child->id) ?? collect())->isNotEmpty()) {
                        return true;
                    }

                    foreach ($child->children as $grandChild) {
                        if (($mediaByFolderId->get($grandChild->id) ?? collect())->isNotEmpty()) {
                            return true;
                        }

                        foreach ($grandChild->children as $greatGrandChild) {
                            if (($mediaByFolderId->get($greatGrandChild->id) ?? collect())->isNotEmpty()) {
                                return true;
                            }
                        }
                    }

                    return false;
                });
            @endphp

            @if ($visibleChildren->isNotEmpty())
                <div class="@if($directMedia->isNotEmpty()) mt-4 @endif space-y-4">
                    @foreach ($visibleChildren as $child)
                        @include('admin.content.media.items.partials.folder-node', [
                            'folder' => $child,
                            'level' => $level + 1,
                            'mediaByFolderId' => $mediaByFolderId,
                        ])
                    @endforeach
                </div>
            @endif

            @if ($directMedia->isEmpty() && $visibleChildren->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                    No media in this folder on this page.
                </div>
            @endif
        </div>
    </details>
</div>