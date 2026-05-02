@extends('frontend.layouts.app')

@section('title', $articleContent->meta_title ?? $articleContent->title)
@section('meta_description', $articleContent->meta_description ?? $articleContent->excerpt ?? '')

@section('content')
    @php
        $cover = $articleContent->mediaUsages->firstWhere('role_key', 'cover');
        $coverMedia = $cover?->media;

        $imageUrl = $coverMedia?->path
            ? \Illuminate\Support\Facades\Storage::url($coverMedia->path)
            : null;

        $primaryCategory = $articleContent->categories->firstWhere('pivot.is_primary', true)
            ?? $articleContent->categories->first();

        $bodyFormat = $article?->body_format ?? 'markdown';
        $body = $article?->body ?? '';
    @endphp

    <article class="mx-auto max-w-5xl px-4 py-10 md:py-16">
        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/80 shadow-2xl shadow-slate-950/60 backdrop-blur">
            <header class="relative overflow-hidden px-6 py-10 md:px-10 md:py-16">
                @if ($imageUrl)
                    <div class="absolute inset-0">
                        <img src="{{ $imageUrl }}" alt="{{ $coverMedia?->alt_text ?: $articleContent->title }}" class="h-full w-full object-cover opacity-35">
                        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/20 via-slate-900/75 to-slate-900"></div>
                    </div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950"></div>
                @endif

                <div class="relative max-w-4xl">
                    @if ($primaryCategory)
                        <span class="inline-flex rounded-full bg-amber-500 px-3 py-1 text-xs font-semibold text-slate-950">
                            {{ $primaryCategory->name }}
                        </span>
                    @endif

                    <h1 class="mt-6 text-3xl font-semibold leading-tight text-white md:text-5xl">
                        {{ $articleContent->title }}
                    </h1>

                    @if ($articleContent->excerpt)
                        <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300">
                            {{ $articleContent->excerpt }}
                        </p>
                    @endif
                </div>
            </header>

            <div class="px-6 pb-10 md:px-10 md:pb-14">
                <div class="border-t border-white/10 pt-8">
                    <div class="max-w-none text-base leading-8 text-slate-300 [&_h2]:mb-4 [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-white [&_p]:mb-5 [&_strong]:text-white">
                        @if ($bodyFormat === 'markdown')
                            {!! \Illuminate\Support\Str::markdown($body) !!}
                        @elseif ($bodyFormat === 'html')
                            {!! $body !!}
                        @else
                            {!! nl2br(e($body)) !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a
                href="{{ url('/articles') }}"
                class="inline-flex items-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
            >
                ← กลับไปหน้าบทความ
            </a>
        </div>
    </article>
@endsection