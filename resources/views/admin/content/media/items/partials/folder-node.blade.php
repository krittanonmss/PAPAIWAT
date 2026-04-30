@php
    $currentFolderId = (string) $folder->id;
    $isActive = $selectedFolderId === $currentFolderId;

    $paddingClass = match ($level) {
        0 => 'pl-0',
        1 => 'pl-4',
        2 => 'pl-8',
        3 => 'pl-12',
        default => 'pl-14',
    };

    $hasActiveDescendant = function ($folder) use (&$hasActiveDescendant, $selectedFolderId) {
        foreach ($folder->children as $child) {
            if ((string) $child->id === (string) $selectedFolderId) {
                return true;
            }

            if ($hasActiveDescendant($child)) {
                return true;
            }
        }

        return false;
    };

    $shouldOpen = $isActive || $hasActiveDescendant($folder);
@endphp

<div class="{{ $paddingClass }}">
    @if ($folder->children->isNotEmpty())
        <details class="group" @if ($shouldOpen) open @endif>
            <summary class="list-none cursor-pointer select-none">
                <div
                    class="{{ $isActive ? 'border-blue-500/30 bg-blue-500/15 text-blue-300' : 'border-transparent text-slate-400 hover:bg-white/5 hover:text-slate-200' }} group/row flex items-center gap-2 rounded-lg border px-2.5 py-2 text-sm transition-all duration-150"
                >
                    <span class="shrink-0 text-slate-500 transition-transform duration-200 group-open:rotate-90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L14 9.586a1 1 0 010 1.414l-5.293 5.293a1 1 0 01-1.414-1.414L11.586 10 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>

                    <a
                        href="{{ route('admin.media.index', ['media_folder_id' => $folder->id]) }}"
                        class="flex min-w-0 flex-1 items-center gap-2"
                        onclick="event.stopPropagation()"
                    >
                        <span class="{{ $isActive ? 'text-blue-400' : 'text-slate-500 group-hover/row:text-slate-400' }} shrink-0 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                            </svg>
                        </span>

                        <span class="min-w-0 flex-1 truncate">
                            {{ $folder->name }}
                        </span>
                    </a>

                    @if ($folder->status !== 'active')
                        <span class="ml-auto shrink-0 rounded-full bg-slate-700/80 px-2 py-0.5 text-[10px] font-medium text-slate-400">
                            ปิด
                        </span>
                    @endif
                </div>
            </summary>

            <div class="ml-4 mt-0.5 space-y-0.5 border-l border-slate-700/50">
                @foreach ($folder->children as $child)
                    @include('admin.content.media.items.partials.folder-node', [
                        'folder' => $child,
                        'level' => $level + 1,
                        'selectedFolderId' => $selectedFolderId,
                    ])
                @endforeach
            </div>
        </details>
    @else
        <a
            href="{{ route('admin.media.index', ['media_folder_id' => $folder->id]) }}"
            class="{{ $isActive ? 'border-blue-500/30 bg-blue-500/15 text-blue-300' : 'border-transparent text-slate-400 hover:bg-white/5 hover:text-slate-200' }} group/row flex items-center gap-2 rounded-lg border px-2.5 py-2 text-sm transition-all duration-150"
        >
            <span class="h-3.5 w-3.5 shrink-0"></span>

            <span class="{{ $isActive ? 'text-blue-400' : 'text-slate-500 group-hover/row:text-slate-400' }} shrink-0 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                </svg>
            </span>

            <span class="min-w-0 flex-1 truncate">
                {{ $folder->name }}
            </span>

            @if ($folder->status !== 'active')
                <span class="ml-auto shrink-0 rounded-full bg-slate-700/80 px-2 py-0.5 text-[10px] font-medium text-slate-400">
                    ปิด
                </span>
            @endif
        </a>
    @endif
</div>