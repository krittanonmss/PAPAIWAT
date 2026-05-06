@php
    $content = $section->content_data ?? [];
    $items = $section->items ?? collect();
    $itemCollection = collect(method_exists($items, 'items') ? $items->items() : $items);
    $filters = $section->filters ?? [];
    $totalItems = method_exists($items, 'total') ? $items->total() : $itemCollection->count();
    $activeSearch = request('search');
    $activeCategory = request('category');
    $activeTag = request('tag');
    $activeAuthor = request('author');
    $activeSort = request('sort');
    $categories = collect($filters['categories'] ?? []);
    $tags = collect($filters['tags'] ?? []);
    $authors = collect($filters['authors'] ?? []);
    $hasActiveFilters = $activeSearch || $activeCategory || $activeTag || $activeAuthor || $activeSort;
@endphp

<section class="bg-slate-950 px-4 py-16 text-white">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 text-center">
            @if(!empty($content['eyebrow']))
                <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
            @endif
            <h1 class="mt-3 text-4xl font-bold md:text-6xl">{{ $content['title'] ?? 'บทความทั้งหมด' }}</h1>
            @if(!empty($content['subtitle']))
                <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-400">{{ $content['subtitle'] }}</p>
            @endif
        </div>

        <div class="mb-8 rounded-3xl border border-white/10 bg-slate-900/75 p-5 shadow-2xl shadow-slate-950/50 backdrop-blur-xl">
            <form method="GET">
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,2fr)_140px] xl:items-end">
                    <label class="block">
                        <span class="mb-2 block text-xs font-medium text-slate-400">ค้นหา</span>
                        <input type="search" name="search" value="{{ $activeSearch }}" placeholder="ชื่อบทความ, ผู้เขียน, tag, keyword" class="h-12 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white placeholder:text-slate-500 focus:border-blue-400/50 focus:outline-none">
                    </label>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="block">
                            <span class="mb-2 block text-xs font-medium text-slate-400">หมวดหมู่</span>
                            <select name="category" class="h-12 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white focus:border-blue-400/50 focus:outline-none">
                                <option value="">ทั้งหมด</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug ?? $category->id }}" @selected((string) $activeCategory === (string) ($category->slug ?? $category->id))>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-xs font-medium text-slate-400">แท็ก</span>
                            <select name="tag" class="h-12 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm text-white focus:border-blue-400/50 focus:outline-none">
                                <option value="">ทั้งหมด</option>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->slug ?? $tag->id }}" @selected((string) $activeTag === (string) ($tag->slug ?? $tag->id))>#{{ $tag->name }}</option>
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
                                <option value="oldest" @selected($activeSort === 'oldest')>เก่าสุด</option>
                            </select>
                        </label>
                    </div>

                    <button class="h-12 rounded-2xl bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-500">ค้นหา</button>
                </div>
            </form>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <span class="rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs text-blue-200">ทั้งหมด {{ number_format($totalItems) }}</span>
                @if ($hasActiveFilters)
                    <a href="{{ url()->current() }}" class="rounded-full border border-blue-300/20 bg-blue-300/10 px-3 py-1 text-xs font-medium text-blue-100 transition hover:bg-blue-300/15">ล้างตัวกรอง</a>
                @endif
            </div>
        </div>

        <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-4">
            @forelse ($itemCollection as $articleContent)
                @php
                    $mediaUsages = ($articleContent && $articleContent->relationLoaded('mediaUsages')) ? $articleContent->mediaUsages : collect();
                    $cover = $mediaUsages->firstWhere('role_key', 'cover');
                    $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                    $path = $coverMedia?->path;
                    $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                @endphp
                <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 transition hover:-translate-y-1 hover:border-blue-300/40">
                    <a href="{{ route('articles.show', $articleContent->slug) }}">
                        <div class="h-64 bg-slate-900">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $articleContent?->title ?: 'Article image' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-slate-500">No Image</div>
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-xs text-blue-300">{{ $articleContent->published_at?->format('d M Y') ?? 'Published' }}</p>
                            <h2 class="mt-2 line-clamp-2 text-2xl font-medium">{{ $articleContent?->title ?? 'Untitled article' }}</h2>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $articleContent?->excerpt ?? 'ยังไม่มีคำโปรย' }}</p>
                        </div>
                    </a>
                </article>
            @empty
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 md:col-span-2 xl:col-span-4">ยังไม่มีบทความ</div>
            @endforelse
        </div>

        @if (method_exists($items, 'links') && $items->hasPages())
            <div class="mt-10 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-xl shadow-slate-950/30">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>
