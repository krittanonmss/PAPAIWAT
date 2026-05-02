@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'รายการวัดแนะนำ')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'รายการวัดแนะนำ')

@section('content')
    @php
        $items = $items ?? collect();
    @endphp

    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="mb-10">
            <p class="text-sm font-medium text-blue-300">Featured Temples</p>
            <h1 class="mt-2 text-3xl font-bold text-white">
                {{ $page->title ?? 'วัดแนะนำ' }}
            </h1>

            @if ($page->excerpt)
                <p class="mt-3 text-slate-400">
                    {{ $page->excerpt }}
                </p>
            @endif
        </div>

        @if ($items->isNotEmpty())
            <div class="space-y-6">
                @foreach ($items as $temple)
                    @php
                        $contentModel = $temple->content;
                        $address = $temple->address;
                        $cover = $contentModel?->mediaUsages?->firstWhere('role_key', 'cover');
                        $imageUrl = $cover?->media?->path
                            ? \Illuminate\Support\Facades\Storage::url($cover->media->path)
                            : null;
                    @endphp

                    <a
                        href="{{ route('temples.show', $temple) }}"
                        class="grid overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur transition hover:border-white/20 md:grid-cols-[280px_minmax(0,1fr)]"
                    >
                        <div class="h-64 bg-slate-900 md:h-full">
                            @if ($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $contentModel?->title ?? 'Temple image' }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-xs text-slate-500">
                                    No Image
                                </div>
                            @endif
                        </div>

                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-white">
                                {{ $contentModel?->title ?? '-' }}
                            </h2>

                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-400">
                                {{ $contentModel?->excerpt ?? '-' }}
                            </p>

                            <p class="mt-5 text-xs text-slate-500">
                                {{ $address?->province ?? '-' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                ยังไม่มีข้อมูลวัด
            </div>
        @endif
    </section>
@endsection