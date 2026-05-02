@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'รายการวัด')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'รายการวัด')

@section('content')
    @php
        $items = $items ?? collect();
    @endphp

    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-white">
                {{ $page->title ?? 'รายการวัด' }}
            </h1>

            @if ($page->excerpt)
                <p class="mt-3 text-slate-400">
                    {{ $page->excerpt }}
                </p>
            @endif
        </div>

        @if ($items->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
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
                        class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:border-white/20"
                    >
                        <div class="h-52 overflow-hidden bg-slate-900">
                            @if ($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $contentModel?->title ?? 'Temple image' }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-xs text-slate-500">
                                    No Image
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2 p-5">
                            <h2 class="line-clamp-1 text-base font-semibold text-white">
                                {{ $contentModel?->title ?? '-' }}
                            </h2>

                            <p class="line-clamp-2 text-sm text-slate-400">
                                {{ $contentModel?->excerpt ?? '-' }}
                            </p>

                            <p class="text-xs text-slate-500">
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