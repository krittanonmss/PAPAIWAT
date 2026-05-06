@php
    $content = $section->content_data ?? [];
    $items = collect($section->items ?? []);
@endphp

<section class="bg-slate-950 px-4 py-16">
    <div class="mx-auto max-w-7xl">
        <div class="mb-7 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                @if(!empty($content['eyebrow']))
                    <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
                @endif
                <h2 class="mt-2 text-3xl font-bold text-white">{{ $content['title'] ?? 'วัดแนะนำ' }}</h2>
                @if(!empty($content['subtitle']))
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">{{ $content['subtitle'] }}</p>
                @endif
            </div>
            <a href="{{ url('/temple-list') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">ดูทั้งหมด</a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @forelse($items as $temple)
                @php
                    $templeContent = $temple->relationLoaded('content') ? $temple->content : null;
                    $mediaUsages = ($templeContent && $templeContent->relationLoaded('mediaUsages')) ? $templeContent->mediaUsages : collect();
                    $cover = $mediaUsages->firstWhere('role_key', 'cover');
                    $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                    $path = $coverMedia?->path;
                    $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                    $address = $temple->relationLoaded('address') ? $temple->address : null;
                @endphp
                <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 transition hover:-translate-y-1 hover:border-blue-300/40">
                    <a href="{{ route('temples.show', $temple) }}">
                        <div class="h-56 bg-slate-900">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $templeContent?->title ?? 'Temple image' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-slate-500">No image</div>
                            @endif
                        </div>
                        <div class="p-5">
                            <p class="text-xs text-blue-300">{{ $address?->province ?? 'ไม่ระบุจังหวัด' }}</p>
                            <h3 class="mt-2 line-clamp-2 text-xl font-semibold text-white">{{ $templeContent?->title ?? '-' }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $templeContent?->excerpt ?? 'ยังไม่มีคำโปรย' }}</p>
                        </div>
                    </a>
                </article>
            @empty
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 md:col-span-2 xl:col-span-4">ยังไม่มีวัดสำหรับแสดงผล</div>
            @endforelse
        </div>
    </div>
</section>
