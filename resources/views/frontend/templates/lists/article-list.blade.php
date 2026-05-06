@extends('frontend.layouts.app')

@section('title', ($page ?? null)?->meta_title ?? ($page ?? null)?->title ?? 'บทความ')
@section('meta_description', ($page ?? null)?->meta_description ?? ($page ?? null)?->excerpt ?? 'รายการบทความ')

@section('content')
@php
    $sections = collect($sections ?? []);
    $items = $items ?? $data ?? collect();
    $itemCollection = collect(method_exists($items, 'items') ? $items->items() : $items);
    $totalItems = method_exists($items, 'total') ? $items->total() : $itemCollection->count();
    $showHero = $showHero ?? true;
    $title = ($page ?? null)?->title ?? ($content['title'] ?? 'บทความ');
    $subtitle = ($page ?? null)?->excerpt ?? ($content['subtitle'] ?? 'อ่านเรื่องราว วัฒนธรรม และข้อมูลที่เกี่ยวข้อง');
    $activeSearch = request('search');
    $activeCategory = request('category');
    $activeTag = request('tag');
    $activeAuthor = request('author');
    $activeSort = request('sort');
    $heroContent = $itemCollection->first();
    $heroMediaUsages = ($heroContent && $heroContent->relationLoaded('mediaUsages')) ? $heroContent->mediaUsages : collect();
    $heroCover = $heroMediaUsages->firstWhere('role_key', 'cover');
    $heroMedia = ($heroCover && $heroCover->relationLoaded('media')) ? $heroCover->media : null;
    $heroPath = $heroMedia?->path;
    $heroUrl = $heroPath ? (filter_var($heroPath, FILTER_VALIDATE_URL) ? $heroPath : \Illuminate\Support\Facades\Storage::url($heroPath)) : null;
    $categories = collect($filters['categories'] ?? [])
        ->whenEmpty(fn () => $itemCollection
        ->flatMap(fn ($articleContent) => ($articleContent && $articleContent->relationLoaded('categories')) ? $articleContent->categories : collect())
        ->unique('id')
        ->values());
    $tags = collect($filters['tags'] ?? []);
    $authors = collect($filters['authors'] ?? []);
    $hasActiveFilters = $activeSearch || $activeCategory || $activeTag || $activeAuthor || $activeSort;
@endphp

@if($sections->isNotEmpty())
    <main class="bg-slate-950 text-white">
        @foreach($sections as $section)
            @include('frontend.templates.sections._renderer', ['section' => $section])
        @endforeach
    </main>
@else
<div class="bg-slate-950 text-white">
    @if ($showHero)
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/20 via-slate-950/75 to-slate-950"></div>
            @if ($heroUrl)
                <img src="{{ $heroUrl }}" alt="{{ $heroMedia?->alt_text ?: $title }}" class="h-[460px] w-full object-cover opacity-70">
            @else
                <div class="h-[420px] bg-gradient-to-br from-slate-900 via-indigo-950/60 to-slate-950"></div>
            @endif
            <div class="absolute inset-0 flex items-center">
                <div class="mx-auto max-w-5xl px-4 text-center">
                    <p class="text-sm font-medium text-blue-300">PAPAIWAT Articles</p>
                    <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">{{ $title }}</h1>
                    <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-300">{{ $subtitle }}</p>
                </div>
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 py-10">
        <div class="{{ $showHero ? '-mt-20' : 'mt-0' }} mb-8 rounded-3xl border border-white/10 bg-slate-900/75 p-5 shadow-2xl shadow-slate-950/50 backdrop-blur-xl">
            <form method="GET">
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,2fr)_140px] xl:items-end">
                    <label class="block">
                        <span class="mb-2 block text-xs font-medium text-slate-400">ค้นหา</span>
                        <input
                            type="search"
                            name="search"
                            value="{{ $activeSearch }}"
                            placeholder="ชื่อบทความ, ผู้เขียน, tag, keyword"
                            class="h-12 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white placeholder:text-slate-500 focus:border-blue-400/50 focus:outline-none"
                        >
                    </label>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="block">
                            <span class="mb-2 block text-xs font-medium text-slate-400">หมวดหมู่</span>
                            <select name="category" class="h-12 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white focus:border-blue-400/50 focus:outline-none">
                                <option value="">ทั้งหมด</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug ?? $category->id }}" @selected((string) $activeCategory === (string) ($category->slug ?? $category->id))>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-xs font-medium text-slate-400">แท็ก</span>
                            <select name="tag" class="h-12 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white focus:border-blue-400/50 focus:outline-none">
                                <option value="">ทั้งหมด</option>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->slug ?? $tag->id }}" @selected((string) $activeTag === (string) ($tag->slug ?? $tag->id))>
                                        #{{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-xs font-medium text-slate-400">ผู้เขียน</span>
                            <select name="author" class="h-12 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white focus:border-blue-400/50 focus:outline-none">
                                <option value="">ทั้งหมด</option>
                                @foreach ($authors as $author)
                                    <option value="{{ $author }}" @selected($activeAuthor === $author)>{{ $author }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-xs font-medium text-slate-400">เรียงตาม</span>
                            <select name="sort" class="h-12 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white focus:border-blue-400/50 focus:outline-none">
                                <option value="">ล่าสุด</option>
                                <option value="popular" @selected($activeSort === 'popular')>ยอดเข้าชมสูงสุด</option>
                                <option value="likes" @selected($activeSort === 'likes')>ถูกใจมากสุด</option>
                                <option value="bookmarks" @selected($activeSort === 'bookmarks')>บันทึกมากสุด</option>
                                <option value="shares" @selected($activeSort === 'shares')>แชร์มากสุด</option>
                                <option value="reading_time" @selected($activeSort === 'reading_time')>อ่านนานสุด</option>
                                <option value="oldest" @selected($activeSort === 'oldest')>เก่าสุด</option>
                            </select>
                        </label>
                    </div>

                    <button class="h-12 rounded-2xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500">ค้นหา</button>
                </div>
            </form>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs text-blue-200">ทั้งหมด {{ number_format($totalItems) }}</span>
                    @if (method_exists($items, 'currentPage') && $items->lastPage() > 1)
                        <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-slate-300">หน้า {{ number_format($items->currentPage()) }} / {{ number_format($items->lastPage()) }}</span>
                    @endif
                    @foreach ($categories->take(8) as $category)
                        <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-slate-300">{{ $category->name }}</span>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ url()->current() }}" class="rounded-full border border-blue-300/20 bg-blue-300/10 px-3 py-1 text-xs font-medium text-blue-100 transition hover:bg-blue-300/15">ล้างตัวกรอง</a>
                    @endif
                    <div class="rounded-full border border-white/10 bg-slate-950/50 px-3 py-1 text-xs text-slate-400">4 x 4 grid</div>
                </div>
            </div>
        </div>

        @if ($itemCollection->isNotEmpty())
            <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($itemCollection as $articleContent)
                    @php
                        $mediaUsages = ($articleContent && $articleContent->relationLoaded('mediaUsages')) ? $articleContent->mediaUsages : collect();
                        $cover = $mediaUsages->firstWhere('role_key', 'cover');
                        $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                        $path = $coverMedia?->path;
                        $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                        $categoriesForItem = ($articleContent && $articleContent->relationLoaded('categories')) ? $articleContent->categories : collect();
                        $category = $categoriesForItem->firstWhere('pivot.is_primary', true) ?? $categoriesForItem->first();
                        $article = ($articleContent && $articleContent->relationLoaded('article')) ? $articleContent->article : null;
                        $tags = ($article && $article->relationLoaded('tags')) ? $article->tags : collect();
                        $stat = ($article && $article->relationLoaded('stat')) ? $article->stat : null;
                    @endphp
                    <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:border-blue-300/40 hover:bg-white/[0.07]">
                        <a href="{{ route('articles.show', $articleContent->slug) }}">
                            <div class="relative h-72 overflow-hidden bg-slate-900">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $coverMedia?->alt_text ?: $articleContent?->title ?: 'Article image' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center text-sm text-slate-500">No Image</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/10 to-transparent"></div>
                                <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-amber-400 px-3 py-1 text-xs font-semibold text-slate-950">{{ $category?->name ?? 'Article' }}</span>
                                </div>
                            </div>

                            <div class="space-y-4 p-6">
                                <div>
                                    <p class="text-xs text-blue-300">{{ $articleContent->published_at?->format('d M Y') ?? 'Published' }}</p>
                                    <h2 class="mt-2 line-clamp-2 text-2xl font-medium text-white">{{ $articleContent?->title ?? 'Untitled article' }}</h2>
                                    @if ($article?->title_en)
                                        <p class="mt-1 line-clamp-1 text-sm text-blue-100">{{ $article->title_en }}</p>
                                    @endif
                                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $articleContent?->excerpt ?? 'ยังไม่มีคำโปรย' }}</p>
                                    @if ($article?->excerpt_en)
                                        <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500">{{ $article->excerpt_en }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2 border-t border-white/10 pt-4 text-xs text-slate-400">
                                    <span class="rounded-full bg-slate-950/50 px-3 py-1">{{ $article?->author_name ?: 'PAPAIWAT Editorial' }}</span>
                                    @if ($article?->reading_time_minutes)
                                        <span class="rounded-full bg-slate-950/50 px-3 py-1">อ่าน {{ number_format($article->reading_time_minutes) }} นาที</span>
                                    @endif
                                    <span class="rounded-full bg-slate-950/50 px-3 py-1">{{ number_format((int) ($stat?->view_count ?? 0)) }} views</span>
                                    @foreach ($tags->take(2) as $tag)
                                        <span class="rounded-full border border-blue-300/20 bg-blue-300/10 px-3 py-1 text-blue-100">#{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            @if (method_exists($items, 'links') && $items->hasPages())
                <div class="mt-10 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-xl shadow-slate-950/30 backdrop-blur">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 backdrop-blur">ยังไม่มีบทความ</div>
        @endif
    </section>
</div>
@endif
@endsection
