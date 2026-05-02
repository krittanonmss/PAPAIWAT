@if ($mediaItems->isEmpty())
    <div class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-4 text-sm text-slate-400">
        ยังไม่มีไฟล์มีเดีย
    </div>
@else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($mediaItems as $media)
            @php
                $mediaUrl = $media->path
                    ? (filter_var($media->path, FILTER_VALIDATE_URL)
                        ? $media->path
                        : \Illuminate\Support\Facades\Storage::url($media->path))
                    : null;

                $isImage = $media->media_type === 'image';
            @endphp

            <label class="relative cursor-pointer overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:border-emerald-400/40 hover:bg-white/[0.06]">
                <input
                    type="checkbox"
                    value="{{ $media->id }}"
                    class="peer sr-only"
                    :checked="selectedGallery.includes('{{ $media->id }}')"
                    @change="toggleGallery('{{ $media->id }}')"
                >

                <div class="aspect-video overflow-hidden bg-slate-950">
                    @if ($isImage && $mediaUrl)
                        <img
                            src="{{ $mediaUrl }}"
                            alt="{{ $media->title ?: $media->original_filename }}"
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
                    <p class="truncate text-sm font-medium text-slate-200">
                        {{ $media->title ?: $media->original_filename }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-500">
                        #{{ $media->id }} · {{ $media->media_type }}
                    </p>
                </div>

                <div class="pointer-events-none absolute inset-0 hidden rounded-2xl border-2 border-emerald-400 peer-checked:block"></div>
            </label>
        @endforeach
    </div>

    @if ($mediaItems->hasPages())
        <div class="mt-5 border-t border-white/10 pt-4">
            {{ $mediaItems->onEachSide(1)->links() }}
        </div>
    @endif
@endif