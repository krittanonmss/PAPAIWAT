@php
    $indentClass = match ($level) {
        0 => '',
        1 => 'ml-6',
        2 => 'ml-12',
        3 => 'ml-16',
        default => 'ml-20',
    };

    $hasChildren = $folder->children->isNotEmpty();
@endphp

<div class="{{ $indentClass }}">
    <details class="group rounded-xl border border-slate-200 bg-white shadow-sm" @if($level < 1) open @endif>
        <summary class="list-none cursor-pointer p-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($hasChildren)
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-slate-300 text-xs text-slate-500 transition group-open:rotate-90">
                                >
                            </span>
                        @else
                            <span class="inline-flex h-5 w-5 items-center justify-center text-xs text-slate-300">
                                •
                            </span>
                        @endif

                        @if ($level > 0)
                            <span class="text-slate-300">└─</span>
                        @endif

                        <h3 class="text-sm font-semibold text-slate-900">
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

                        @if ($hasChildren)
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                {{ $folder->children->count() }} children
                            </span>
                        @endif
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                        <span>Slug: {{ $folder->slug }}</span>
                        <span>Parent: {{ $folder->parent?->name ?? 'Root Folder' }}</span>
                        <span>Sort: {{ $folder->sort_order }}</span>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <a
                        href="{{ route('admin.media-folders.edit', $folder) }}"
                        class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        onclick="event.stopPropagation()"
                    >
                        Edit
                    </a>

                    <form
                        method="POST"
                        action="{{ route('admin.media-folders.destroy', $folder) }}"
                        onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this folder?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </summary>

        <div class="border-t border-slate-100 px-4 pb-4">
            <div class="pt-4 text-sm text-slate-500">
                {{ $folder->description ?: 'ไม่มีคำอธิบาย' }}
            </div>

            @if ($hasChildren)
                <div class="mt-4 space-y-3">
                    @foreach ($folder->children as $child)
                        @include('admin.content.media.folders.partials.folder-node', [
                            'folder' => $child,
                            'level' => $level + 1,
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </details>
</div>