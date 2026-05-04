@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'รายการ')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'รายการข้อมูล')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-12">
        <header class="mb-8">
            <p class="text-sm font-medium text-blue-300">{{ $page->excerpt ?? 'Listing' }}</p>
            <h1 class="mt-3 text-3xl font-bold text-white md:text-5xl">{{ $page->title ?? 'รายการข้อมูล' }}</h1>
            @if ($page->description)
                <p class="mt-4 max-w-3xl text-slate-400">{{ $page->description }}</p>
            @endif
        </header>

        @if (($items ?? collect())->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($items as $item)
                    @php
                        $content = $item->content ?? $item;
                        $cover = $content?->mediaUsages?->firstWhere('role_key', 'cover');
                        $imageUrl = $cover?->media?->path
                            ? (filter_var($cover->media->path, FILTER_VALIDATE_URL) ? $cover->media->path : \Illuminate\Support\Facades\Storage::url($cover->media->path))
                            : null;
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] backdrop-blur">
                        <div class="h-44 bg-slate-900">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $cover?->media?->alt_text ?: ($content?->title ?? 'Preview image') }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-slate-500">No Image</div>
                            @endif
                        </div>
                        <div class="p-5">
                            <h2 class="text-lg font-semibold text-white">{{ $content?->title ?? 'Untitled' }}</h2>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $content?->excerpt ?? 'ยังไม่มีคำอธิบาย' }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400">
                ยังไม่มีข้อมูลสำหรับแสดงผล
            </div>
        @endif
    </section>
@endsection
