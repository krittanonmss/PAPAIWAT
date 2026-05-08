@extends('frontend.layouts.app')

@section('title', ($articleContent ?? null)?->meta_title ?? ($articleContent ?? null)?->title ?? 'Article Detail')
@section('meta_description', ($articleContent ?? null)?->meta_description ?? ($articleContent ?? null)?->excerpt ?? 'Article Detail')

@section('content')
@php
    $articleContent = $articleContent ?? $content ?? null;
    $article = $article ?? (($articleContent && $articleContent->relationLoaded('article')) ? $articleContent->article : null);

    $mediaUsages = ($articleContent && $articleContent->relationLoaded('mediaUsages'))
        ? $articleContent->mediaUsages
        : collect();

    $categories = ($articleContent && $articleContent->relationLoaded('categories'))
        ? $articleContent->categories
        : collect();

    $tags = ($article && $article->relationLoaded('tags'))
        ? $article->tags
        : collect();

    $cover = $mediaUsages->firstWhere('role_key', 'cover');
    $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
    $coverPath = $coverMedia?->path;

    $coverUrl = $coverPath
        ? (filter_var($coverPath, FILTER_VALIDATE_URL)
            ? $coverPath
            : \Illuminate\Support\Facades\Storage::url($coverPath))
        : null;

    $category = $categories->firstWhere('pivot.is_primary', true) ?? $categories->first();

    $bodyFormat = $article?->body_format ?? 'markdown';
    $body = $article?->body ?? '';

    $publishedAt = $articleContent?->published_at;
    $authorName = $article?->author_name ?: 'PAPAIWAT Editorial';
    $readingTime = $article?->reading_time_minutes;
    $approvedComments = $approvedComments ?? collect();
    $favoritePayload = $article ? [
        'type' => 'article',
        'id' => $article->id,
        'title' => $articleContent?->title,
        'url' => route('articles.show', $articleContent?->slug),
        'excerpt' => $articleContent?->excerpt ?? $article?->excerpt_en,
        'image' => $coverUrl,
    ] : [];
@endphp

<main class="relative min-h-screen overflow-hidden bg-slate-950 px-4 py-8 text-white">
    {{-- Background --}}
    <div class="fixed inset-0">
        @if ($coverUrl)
            <img
                src="{{ $coverUrl }}"
                alt=""
                class="h-full w-full scale-110 object-cover opacity-25 blur-md"
            >
        @else
            <div class="h-full w-full bg-gradient-to-br from-slate-950 via-indigo-950/70 to-slate-900"></div>
        @endif

        <div class="absolute inset-0 bg-slate-950/80"></div>
    </div>

    {{-- Article Card --}}
    <article class="relative mx-auto max-w-4xl overflow-hidden rounded-[1.75rem] border border-white/10 bg-slate-800/90 shadow-2xl shadow-slate-950/70 backdrop-blur-xl">
        {{-- Cover --}}
        <section class="relative h-[420px] overflow-hidden">
            @if ($coverUrl)
                <img
                    src="{{ $coverUrl }}"
                    alt="{{ $coverMedia?->alt_text ?: $articleContent?->title ?: 'Article cover' }}"
                    class="h-full w-full object-cover"
                >
            @else
                <div class="h-full w-full bg-gradient-to-br from-slate-800 via-indigo-950/50 to-slate-950"></div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-slate-800 via-slate-900/45 to-transparent"></div>

            <div class="absolute inset-x-0 bottom-0 px-7 pb-8">
                @if ($category)
                    <span class="inline-flex rounded-full bg-emerald-500 px-3 py-1 text-xs font-semibold text-white">
                        {{ $category->name }}
                    </span>
                @endif

                <h1 class="mt-5 max-w-3xl text-3xl font-light leading-tight text-white md:text-4xl">
                    {{ $articleContent?->title ?? 'รายละเอียดบทความ' }}
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    {{ $articleContent?->excerpt ?? $article?->excerpt_en ?? 'ยังไม่มีคำโปรย' }}
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-6 text-xs text-slate-300">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                        </svg>
                        {{ $authorName }}
                    </span>

                    @if ($readingTime)
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ number_format($readingTime) }} นาที
                        </span>
                    @endif

                    @if ($article?->lesson_count ?? false)
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75v12m0-12c-2.25-1.5-5.25-1.5-7.5 0v12c2.25-1.5 5.25-1.5 7.5 0m0-12c2.25-1.5 5.25-1.5 7.5 0v12c-2.25-1.5-5.25-1.5-7.5 0" />
                            </svg>
                            {{ number_format($article->lesson_count) }} บทเรียน
                        </span>
                    @endif

                    @if ($publishedAt)
                        <span class="inline-flex items-center gap-2">
                            {{ $publishedAt->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </section>

        {{-- Content --}}
        <section class="px-7 py-8 md:px-8 md:py-10">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($articleContent?->description)
                <p class="mb-8 text-base leading-8 text-slate-300">
                    {!! nl2br(e($articleContent->description)) !!}
                </p>
            @endif

            <div class="max-w-none text-base leading-8 text-slate-300
                [&_a]:text-blue-300
                [&_blockquote]:border-l-4 [&_blockquote]:border-blue-300/40 [&_blockquote]:pl-4 [&_blockquote]:text-slate-400
                [&_h2]:mb-4 [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-white
                [&_h3]:mb-3 [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-white
                [&_li]:mb-1
                [&_ol]:mb-6 [&_ol]:list-decimal [&_ol]:pl-5
                [&_p]:mb-6
                [&_strong]:text-white
                [&_ul]:mb-6 [&_ul]:list-disc [&_ul]:pl-5"
            >
                @if ($body)
                    @if ($bodyFormat === 'markdown')
                        {!! \Illuminate\Support\Str::markdown($body) !!}
                    @elseif ($bodyFormat === 'html')
                        {!! $body !!}
                    @else
                        {!! nl2br(e($body)) !!}
                    @endif
                @else
                    <p class="text-slate-500">ยังไม่มีเนื้อหาบทความ</p>
                @endif
            </div>

            @if ($tags->isNotEmpty())
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs text-slate-300">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                @if ($article)
                    <button
                        type="button"
                        data-local-favorite-toggle
                        data-favorite='{{ e(json_encode($favoritePayload)) }}'
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white/10 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/15"
                    >
                        <svg data-favorite-icon class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h12A1.25 1.25 0 0 1 19.25 5v15.25L12 16.5l-7.25 3.75V5A1.25 1.25 0 0 1 6 3.75Z" />
                        </svg>
                        <span data-favorite-unsaved>บันทึก</span>
                        <span data-favorite-saved class="hidden">บันทึกแล้ว</span>
                    </button>
                @endif

                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/15"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 12 16.5 6.75M7.5 12l9 5.25M7.5 12a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm13.5-6.75a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm0 13.5a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    แชร์
                </button>
            </div>

            <section class="mt-10 border-t border-white/10 pt-8">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-semibold text-white">ความคิดเห็น</h2>
                    <span class="text-sm text-slate-500">{{ number_format($approvedComments->count()) }} รายการ</span>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($approvedComments as $comment)
                        <article class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-sm font-medium text-white">{{ $comment->display_name ?: 'ผู้เยี่ยมชม' }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">{{ $comment->body }}</p>
                            <form method="POST" action="{{ route('comments.report', $comment) }}" class="mt-3 text-right">
                                @csrf
                                <input type="hidden" name="reason" value="ไม่เหมาะสม">
                                <button type="submit" class="text-xs text-slate-500 transition hover:text-red-300">รายงาน</button>
                            </form>
                        </article>
                    @empty
                        <p class="text-sm text-slate-500">ยังไม่มีความคิดเห็น</p>
                    @endforelse

                </div>

                @if ($article)
                    <form method="POST" action="{{ route('articles.comments.store', $article) }}" class="mt-6 space-y-3">
                        @csrf
                        <input
                            type="text"
                            name="display_name"
                            value="{{ old('display_name') }}"
                            placeholder="ชื่อที่ต้องการแสดง"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-300 focus:outline-none"
                        >
                        <textarea
                            name="body"
                            rows="4"
                            required
                            placeholder="เขียนความคิดเห็น"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-blue-300 focus:outline-none"
                        >{{ old('body') }}</textarea>
                        <button type="submit" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-500">
                            ส่งความคิดเห็น
                        </button>
                    </form>
                @endif
            </section>
        </section>
    </article>
</main>
@include('frontend.partials.local_favorites_script')
@endsection
