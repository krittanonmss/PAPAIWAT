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
    <details class="group rounded-2xl border border-white/10 bg-slate-900/70 shadow-sm" @if($level < 1) open @endif>
        <summary class="list-none cursor-pointer p-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($hasChildren)
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-white/10 bg-slate-950 text-xs text-slate-400 transition group-open:rotate-90">
                                >
                            </span>
                        @else
                            <span class="inline-flex h-5 w-5 items-center justify-center text-xs text-slate-500">
                                •
                            </span>
                        @endif

                        @if ($level > 0)
                            <span class="text-slate-600">└─</span>
                        @endif

                        <h3 class="text-sm font-semibold text-white">
                            {{ $folder->name }}
                        </h3>

                        @if ($folder->status === 'active')
                            <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-300">
                                เปิดใช้งาน
                            </span>
                        @else
                            <span class="inline-flex rounded-full border border-slate-500/20 bg-slate-700/40 px-2.5 py-1 text-xs font-medium text-slate-300">
                                ปิดใช้งาน
                            </span>
                        @endif

                        @if ($hasChildren)
                            <span class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-2.5 py-1 text-xs font-medium text-blue-300">
                                {{ $folder->children->count() }} โฟลเดอร์ย่อย
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
                        class="inline-flex items-center rounded-xl border border-white/10 px-3 py-2 text-sm font-medium text-slate-300 hover:bg-white/5"
                        onclick="event.stopPropagation()"
                    >
                        แก้ไข
                    </a>

                    <form
                        method="POST"
                        action="{{ route('admin.media-folders.destroy', $folder) }}"
                        onsubmit="event.stopPropagation(); return confirm('ต้องการลบโฟลเดอร์นี้ใช่หรือไม่?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-xl border border-red-400/20 px-3 py-2 text-sm font-medium text-red-300 hover:bg-red-500/10"
                        >
                            ลบ
                        </button>
                    </form>
                </div>
            </div>
        </summary>

        <div class="border-t border-white/10 px-4 pb-4">
            <div class="pt-4 text-sm text-slate-400">
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