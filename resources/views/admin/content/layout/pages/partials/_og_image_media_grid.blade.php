@if ($mediaItems->isEmpty())
    <div class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-4 text-sm text-slate-400">
        ไม่พบรูปภาพในคลังสื่อ
    </div>
@else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <label
            class="relative cursor-pointer overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:border-blue-400/40 hover:bg-white/[0.06]"
            x-show="!ogImageSearch"
        >
            <input
                type="radio"
                value=""
                class="peer sr-only"
                :checked="selectedOgImage === ''"
                @change="selectedOgImage = ''"
            >

            <div class="flex aspect-video items-center justify-center bg-slate-950/70 text-xs text-slate-500">
                ไม่ใช้ Cover / OG Image
            </div>

            <div class="border-t border-white/10 p-3">
                <p class="text-sm font-medium text-slate-200">ไม่ระบุรูป</p>
                <p class="mt-0.5 text-xs text-slate-500">ใช้รูปเริ่มต้นของระบบ</p>
            </div>

            <div class="pointer-events-none absolute inset-0 hidden rounded-2xl border-4 border-blue-300 bg-blue-500/10 ring-4 ring-blue-400/30 peer-checked:block"></div>
            <div class="pointer-events-none absolute right-3 top-3 hidden rounded-full bg-blue-500 px-3 py-1 text-xs font-semibold text-white shadow-lg shadow-blue-950/40 peer-checked:block">
                เลือกแล้ว
            </div>
        </label>

        @foreach($mediaItems as $image)
            @php
                $imageUrl = $image->path
                    ? (filter_var($image->path, FILTER_VALIDATE_URL)
                        ? $image->path
                        : \Illuminate\Support\Facades\Storage::url($image->path))
                    : null;
                $imageTitle = $image->title ?: $image->original_filename;
            @endphp

            <label class="relative cursor-pointer overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:border-blue-400/40 hover:bg-white/[0.06]">
                <input
                    type="radio"
                    value="{{ $image->id }}"
                    class="peer sr-only"
                    :checked="selectedOgImage === '{{ $image->id }}'"
                    @change="selectedOgImage = '{{ $image->id }}'"
                >

                <div class="aspect-video overflow-hidden bg-slate-950">
                    @if ($imageUrl)
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $imageTitle }}"
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
                        {{ $imageTitle }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-500">
                        #{{ $image->id }} · {{ $image->media_type }}
                    </p>
                </div>

                <div class="pointer-events-none absolute inset-0 hidden rounded-2xl border-4 border-blue-300 bg-blue-500/10 ring-4 ring-blue-400/30 peer-checked:block"></div>
                <div class="pointer-events-none absolute right-3 top-3 hidden rounded-full bg-blue-500 px-3 py-1 text-xs font-semibold text-white shadow-lg shadow-blue-950/40 peer-checked:block">
                    เลือกแล้ว
                </div>
            </label>
        @endforeach
    </div>

    @if ($mediaItems->hasPages())
        <div class="mt-5 border-t border-white/10 pt-4">
            {{ $mediaItems->onEachSide(1)->links() }}
        </div>
    @endif
@endif
