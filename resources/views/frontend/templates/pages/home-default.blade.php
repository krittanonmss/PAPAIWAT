@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'PAPAIWAT')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'PAPAIWAT Platform')

@section('content')
    @php
        $sections = collect($sections ?? []);
        $homeTemples = collect($homeTemples ?? []);
        $homeArticles = collect($homeArticles ?? []);
    @endphp

    @if($sections->isNotEmpty())
        <main class="bg-slate-950 text-white">
            @foreach($sections as $section)
                @include('frontend.templates.sections._renderer', ['section' => $section])
            @endforeach
        </main>
    @else
    <main class="bg-slate-950 text-white">
        <section class="relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/60 to-slate-950"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/20 via-slate-950/75 to-slate-950"></div>

            <div class="absolute inset-0 flex items-center">
                <div class="mx-auto max-w-5xl px-4 text-center">
                    <p class="text-sm font-medium text-blue-300">PAPAIWAT</p>
                    <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                        {{ $page->title ?? 'ค้นพบวัดและวัฒนธรรมไทย' }}
                    </h1>
                    <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-300">
                        {{ $page->excerpt ?? 'สำรวจวัด บทความ และข้อมูลวัฒนธรรมไทยจากฐานข้อมูลที่จัดการผ่าน CMS' }}
                    </p>

                    <div class="mt-7 flex flex-wrap justify-center gap-3">
                        <a href="{{ url('/temple-list') }}" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500">สำรวจวัด</a>
                        <a href="{{ url('/article-list') }}" class="rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white">อ่านบทความ</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-14">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-300">PAPAIWAT Temples</p>
                    <h2 class="mt-2 text-3xl font-bold">วัดแนะนำ</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">วัดเด่นและวัดยอดนิยมที่คัดจากฐานข้อมูล</p>
                </div>
                <a href="{{ url('/temple-list') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">ดูวัดทั้งหมด</a>
            </div>

            @if ($homeTemples->isNotEmpty())
                <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($homeTemples as $temple)
                        @php
                            $content = $temple->relationLoaded('content') ? $temple->content : null;
                            $mediaUsages = ($content && $content->relationLoaded('mediaUsages')) ? $content->mediaUsages : collect();
                            $cover = $mediaUsages->firstWhere('role_key', 'cover');
                            $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                            $path = $coverMedia?->path;
                            $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                            $address = $temple->relationLoaded('address') ? $temple->address : null;
                            $stat = $temple->relationLoaded('stat') ? $temple->stat : null;
                            $categories = ($content && $content->relationLoaded('categories')) ? $content->categories : collect();
                            $category = $categories->firstWhere('pivot.is_primary', true) ?? $categories->first();
                        @endphp

                        <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:border-blue-300/40 hover:bg-white/[0.07]">
                            <a href="{{ route('temples.show', $temple) }}">
                                <div class="relative h-72 overflow-hidden bg-slate-900">
                                    @if ($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $coverMedia?->alt_text ?: $content?->title ?: 'Temple image' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm text-slate-500">No Image</div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/10 to-transparent"></div>
                                    <span class="absolute left-4 top-4 rounded-full bg-amber-400 px-3 py-1 text-xs font-semibold text-slate-950">{{ $temple->temple_type ?: 'Temple' }}</span>
                                    @if ($category)
                                        <span class="absolute bottom-4 left-4 rounded-xl bg-slate-950/60 px-3 py-1 text-xs text-white backdrop-blur">{{ $category->name }}</span>
                                    @endif
                                </div>
                                <div class="space-y-4 p-6">
                                    <div>
                                        <h3 class="line-clamp-2 text-2xl font-medium">{{ $content?->title ?? '-' }}</h3>
                                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $content?->excerpt ?: ($content?->description ? \Illuminate\Support\Str::limit(trim(strip_tags($content->description)), 140) : 'ยังไม่มีคำโปรย') }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2 border-t border-white/10 pt-4 text-xs text-slate-400">
                                        <span class="rounded-full bg-slate-950/50 px-3 py-1">{{ $address?->province ?? 'ไม่ระบุจังหวัด' }}</span>
                                        @if ($stat?->average_rating)
                                            <span class="rounded-full bg-slate-950/50 px-3 py-1">{{ number_format((float) $stat->average_rating, 1) }} rating</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400">ยังไม่มีวัดแนะนำ</div>
            @endif
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-16">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-300">PAPAIWAT Articles</p>
                    <h2 class="mt-2 text-3xl font-bold">Article แนะนำ</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-400">บทความเด่นและเรื่องราวที่น่าสนใจจากฐานข้อมูล</p>
                </div>
                <a href="{{ url('/articles') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">ดูบทความทั้งหมด</a>
            </div>

            @if ($homeArticles->isNotEmpty())
                <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($homeArticles as $articleContent)
                        @php
                            $mediaUsages = $articleContent->relationLoaded('mediaUsages') ? $articleContent->mediaUsages : collect();
                            $cover = $mediaUsages->firstWhere('role_key', 'cover');
                            $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                            $path = $coverMedia?->path;
                            $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                            $categories = $articleContent->relationLoaded('categories') ? $articleContent->categories : collect();
                            $category = $categories->firstWhere('pivot.is_primary', true) ?? $categories->first();
                            $article = $articleContent->relationLoaded('article') ? $articleContent->article : null;
                            $tags = ($article && $article->relationLoaded('tags')) ? $article->tags : collect();
                            $stat = ($article && $article->relationLoaded('stat')) ? $article->stat : null;
                        @endphp

                        <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:border-blue-300/40 hover:bg-white/[0.07]">
                            <a href="{{ route('articles.show', $articleContent->slug) }}">
                                <div class="relative h-72 overflow-hidden bg-slate-900">
                                    @if ($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $coverMedia?->alt_text ?: $articleContent->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm text-slate-500">No Image</div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/10 to-transparent"></div>
                                    <span class="absolute left-4 top-4 rounded-full bg-amber-400 px-3 py-1 text-xs font-semibold text-slate-950">{{ $category?->name ?? 'Article' }}</span>
                                </div>
                                <div class="space-y-4 p-6">
                                    <div>
                                        <p class="text-xs text-blue-300">{{ $articleContent->published_at?->format('d M Y') ?? 'Published' }}</p>
                                        <h3 class="mt-2 line-clamp-2 text-2xl font-medium">{{ $articleContent->title }}</h3>
                                        @if ($article?->title_en)
                                            <p class="mt-1 line-clamp-1 text-sm text-blue-100">{{ $article->title_en }}</p>
                                        @endif
                                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $articleContent->excerpt ?? 'ยังไม่มีคำโปรย' }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2 border-t border-white/10 pt-4 text-xs text-slate-400">
                                        <span class="rounded-full bg-slate-950/50 px-3 py-1">{{ $article?->author_name ?: 'PAPAIWAT Editorial' }}</span>
                                        @if ($article?->reading_time_minutes)
                                            <span class="rounded-full bg-slate-950/50 px-3 py-1">อ่าน {{ number_format($article->reading_time_minutes) }} นาที</span>
                                        @endif
                                        <span class="rounded-full bg-slate-950/50 px-3 py-1">{{ number_format((int) ($stat?->view_count ?? 0)) }} views</span>
                                        @foreach ($tags->take(1) as $tag)
                                            <span class="rounded-full border border-blue-300/20 bg-blue-300/10 px-3 py-1 text-blue-100">#{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400">ยังไม่มี Article แนะนำ</div>
            @endif
        </section>
    </main>
    @endif
@endsection
