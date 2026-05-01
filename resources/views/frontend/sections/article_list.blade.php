@php
    use App\Models\Content\Content;
    use Illuminate\Support\Facades\Storage;

    $limit = (int) ($settings['limit'] ?? 12);

    $articles = Content::query()
        ->where('content_type', 'article')
        ->where('status', 'published')
        ->with([
            'article',
            'categories',
            'mediaUsages.media',
        ])
        ->latest('published_at')
        ->limit($limit)
        ->get();
@endphp

<section class="relative py-16">
    <div class="mx-auto max-w-6xl px-4">

        {{-- Header --}}
        <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-white">
                    {{ $content['title'] ?? 'บทความล่าสุด' }}
                </h2>

                @if (!empty($content['subtitle']))
                    <p class="mt-2 text-slate-400">
                        {{ $content['subtitle'] }}
                    </p>
                @endif
            </div>

            @if (!empty($content['show_view_all']) && !empty($content['view_all_url']))
                <a
                    href="{{ $content['view_all_url'] }}"
                    class="text-sm font-medium text-slate-300 hover:text-white"
                >
                    ดูทั้งหมด →
                </a>
            @endif
        </div>

        {{-- Grid --}}
        @if ($articles->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($articles as $articleContent)
                    @php
                        $article = $articleContent->article;

                        $coverUsage = $articleContent->mediaUsages->firstWhere('role_key', 'cover');
                        $coverMedia = $coverUsage?->media;

                        $imageUrl = $coverMedia?->path
                            ? (filter_var($coverMedia->path, FILTER_VALIDATE_URL)
                                ? $coverMedia->path
                                : Storage::url($coverMedia->path))
                            : null;

                        $primaryCategory = $articleContent->categories->firstWhere('pivot.is_primary', true)
                            ?? $articleContent->categories->first();

                        $publishedDate = $articleContent->published_at
                            ? $articleContent->published_at->format('d/m/Y')
                            : null;
                    @endphp

                    <a
                        href="{{ route('articles.show', $articleContent->slug) }}"
                        class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:border-white/20"
                    >
                        {{-- Image --}}
                        <div class="relative h-52 w-full overflow-hidden bg-slate-900">
                            @if ($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $coverMedia?->alt_text ?: $articleContent->title }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-xs text-slate-500">
                                    No Image
                                </div>
                            @endif

                            @if ($articleContent->is_featured)
                                <span class="absolute left-3 top-3 rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-medium text-white">
                                    แนะนำ
                                </span>
                            @endif

                            @if ($articleContent->is_popular)
                                <span class="absolute right-3 top-3 rounded-full bg-blue-500 px-2 py-0.5 text-[10px] font-medium text-white">
                                    ยอดนิยม
                                </span>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="space-y-3 p-5">
                            <div>
                                <h3 class="line-clamp-1 text-base font-semibold text-white">
                                    {{ $articleContent->title }}
                                </h3>

                                <p class="mt-1 line-clamp-2 text-sm text-slate-400">
                                    {{ $articleContent->excerpt ?: $articleContent->description ?: '-' }}
                                </p>
                            </div>

                            <div class="space-y-1.5 text-xs text-slate-400">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="line-clamp-1">
                                        {{ $primaryCategory?->name ?? 'บทความ' }}
                                    </span>

                                    @if ($article?->reading_time_minutes)
                                        <span class="shrink-0 text-amber-300">
                                            {{ $article->reading_time_minutes }} นาที
                                        </span>
                                    @endif
                                </div>

                                @if ($article?->author_name)
                                    <div class="line-clamp-1 text-slate-500">
                                        {{ $article->author_name }}
                                    </div>
                                @endif

                                @if ($publishedDate)
                                    <div class="text-slate-500">
                                        เผยแพร่ {{ $publishedDate }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between border-t border-white/10 pt-3 text-[11px] text-slate-500">
                                <span>อ่านบทความ</span>
                                <span class="text-blue-300">ดูรายละเอียด →</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                {{ $content['empty_text'] ?? 'ยังไม่มีบทความที่เผยแพร่ในขณะนี้' }}
            </div>
        @endif
    </div>
</section>