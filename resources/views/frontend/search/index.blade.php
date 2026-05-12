@extends('frontend.layouts.app')

@section('title', $queryText !== '' ? 'ค้นหา: '.$queryText : 'ค้นหา')
@section('meta_description', 'ค้นหาวัดและบทความใน PAPAIWAT')

@section('content')
@php
    $items = collect($results->items());
@endphp

<section class="bg-slate-950 px-4 py-14 text-white">
    <div class="mx-auto max-w-7xl">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold text-blue-300">PAPAIWAT Search</p>
            <h1 class="mt-3 text-3xl font-bold md:text-5xl">ค้นหาวัดและบทความ</h1>
            <p class="mt-4 text-sm leading-7 text-slate-400">พิมพ์คำที่ต้องการค้นหา ระบบจะแสดงข้อมูลวัดและบทความที่ตรงกับคำค้น</p>
        </div>

        <form method="GET" action="{{ route('search') }}" class="mt-8 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-3 sm:flex-row">
                <input
                    type="search"
                    name="q"
                    value="{{ $queryText }}"
                    placeholder="ค้นหาชื่อวัด จังหวัด หมวดหมู่ บทความ หรือคำสำคัญ..."
                    class="min-h-12 flex-1 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white placeholder:text-slate-500 focus:border-blue-400/50 focus:outline-none"
                >
                <button type="submit" class="min-h-12 rounded-2xl bg-white px-5 text-sm font-semibold text-slate-950 transition hover:bg-slate-200">ค้นหา</button>
            </div>
        </form>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-400">
                @if($queryText !== '')
                    พบ <span class="font-semibold text-white">{{ number_format($results->total()) }}</span> รายการสำหรับ “{{ $queryText }}”
                @else
                    ใส่คำค้นเพื่อเริ่มค้นหา
                @endif
            </p>
            @if($queryText !== '')
                <a href="{{ route('search') }}" class="text-sm font-medium text-slate-300 transition hover:text-white">ล้างคำค้น</a>
            @endif
        </div>

        @if($items->isNotEmpty())
            <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach($items as $item)
                    @php
                        $mediaUsages = $item->relationLoaded('mediaUsages') ? $item->mediaUsages : collect();
                        $cover = $mediaUsages->firstWhere('role_key', 'cover');
                        $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                        $path = $coverMedia?->path;
                        $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                        $temple = $item->relationLoaded('temple') ? $item->temple : null;
                        $category = $item->relationLoaded('categories') ? $item->categories->first() : null;
                        $label = $item->content_type === 'temple'
                            ? ($temple?->address?->province ?: 'วัด')
                            : 'บทความ';
                        $href = $item->content_type === 'temple' && $temple
                            ? route('temples.show', $temple)
                            : route('articles.show', $item->slug);
                    @endphp
                    <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 transition hover:-translate-y-1 hover:border-blue-300/40">
                        <a href="{{ $href }}">
                            <div class="relative h-56 bg-slate-900">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="flex h-full items-center justify-center text-sm text-slate-500">No image</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                                <span class="absolute left-4 top-4 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white backdrop-blur">{{ $label }}</span>
                            </div>
                            <div class="p-5">
                                <p class="text-xs text-blue-300">{{ $category?->name ?? ($item->content_type === 'temple' ? 'Temple' : 'Article') }}</p>
                                <h2 class="mt-2 line-clamp-2 text-xl font-semibold text-white">{{ $item->title }}</h2>
                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-400">{{ $item->excerpt ?: strip_tags((string) $item->description) }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            @if($results->hasPages())
                <div class="mt-10 rounded-3xl border border-white/10 bg-white/[0.04] p-4">
                    {{ $results->links() }}
                </div>
            @endif
        @elseif($queryText !== '')
            <div class="mt-8 rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400">ไม่พบข้อมูลที่ตรงกับคำค้น</div>
        @endif
    </div>
</section>
@endsection
