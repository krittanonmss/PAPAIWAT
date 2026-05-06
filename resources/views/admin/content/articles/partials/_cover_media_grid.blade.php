@if ($mediaItems->isEmpty())
    <div class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-4 text-sm text-slate-400">
        ยังไม่มีไฟล์รูปภาพ
    </div>
@else
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        <label
            class="relative cursor-pointer overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:border-blue-400/40 hover:bg-white/[0.06]"
            x-show="!mediaSearch"
        >
            <input
                type="radio"
                value=""
                class="peer sr-only"
                :checked="selectedCover === ''"
                @change="selectedCover = ''"
            >

            <div class="flex aspect-video min-h-44 items-center justify-center bg-slate-950/70 text-sm text-slate-500">
                ไม่ใช้รูปหน้าปก
            </div>

            <div class="border-t border-white/10 p-3">
                <p class="text-base font-medium text-slate-200">ไม่ระบุรูปหน้าปก</p>
                <p class="mt-0.5 text-xs text-slate-500">ใช้ fallback ของหน้า article</p>
            </div>

            <div class="pointer-events-none absolute inset-0 hidden rounded-2xl border-2 border-blue-400 peer-checked:block"></div>
        </label>

        @foreach ($mediaItems as $media)
            @php
                $mediaUrl = $media->path
                    ? (filter_var($media->path, FILTER_VALIDATE_URL)
                        ? $media->path
                        : \Illuminate\Support\Facades\Storage::url($media->path))
                    : null;

                $title = $media->title ?: $media->original_filename;
                $searchText = mb_strtolower(trim($title . ' ' . $media->original_filename . ' ' . $media->id));
            @endphp

            <label
                class="relative cursor-pointer overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:border-blue-400/40 hover:bg-white/[0.06]"
                data-media-card
                data-search-text="{{ $searchText }}"
                x-show="!mediaSearch || $el.dataset.searchText.includes(mediaSearch.toLowerCase().trim())"
            >
                <input
                    type="radio"
                    value="{{ $media->id }}"
                    class="peer sr-only"
                    :checked="selectedCover === '{{ $media->id }}'"
                    @change="selectedCover = '{{ $media->id }}'"
                >

                <div class="aspect-video min-h-44 overflow-hidden bg-slate-950">
                    @if ($mediaUrl)
                        <img
                            src="{{ $mediaUrl }}"
                            alt="{{ $title }}"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        >
                    @else
                        <div class="flex h-full w-full items-center justify-center text-xs text-slate-500">
                            ไม่มีตัวอย่างรูป
                        </div>
                    @endif
                </div>

                <div class="border-t border-white/10 p-3">
                    <p class="truncate text-base font-medium text-slate-200">
                        {{ $title }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-500">
                        #{{ $media->id }} · {{ $media->media_type }}
                    </p>
                </div>

                <div class="pointer-events-none absolute inset-0 hidden rounded-2xl border-2 border-blue-400 peer-checked:block"></div>
            </label>
        @endforeach
    </div>

    @if ($mediaItems->hasPages())
        <div class="mt-5 border-t border-white/10 pt-4">
            {{ $mediaItems->onEachSide(1)->links() }}
        </div>
    @endif
@endif
