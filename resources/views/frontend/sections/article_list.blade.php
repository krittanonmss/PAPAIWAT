<section class="relative py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="mb-10 flex items-end justify-between gap-4">
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
        </div>

        @if ($data && $data->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ($data as $articleContent)
                    @php
                        $cover = $articleContent->mediaUsages?->firstWhere('role_key', 'cover');
                        $imageUrl = $cover?->media?->path
                            ? \Illuminate\Support\Facades\Storage::url($cover->media->path)
                            : null;
                    @endphp

                    <a
                        href="{{ route('articles.show', $articleContent->slug) }}"
                        class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:border-white/20"
                    >
                        <div class="h-44 overflow-hidden bg-slate-900">
                            @if ($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $articleContent->title }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-xs text-slate-500">
                                    No Image
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="line-clamp-2 text-base font-semibold text-white">
                                {{ $articleContent->title }}
                            </h3>

                            <p class="mt-2 line-clamp-2 text-sm text-slate-400">
                                {{ $articleContent->excerpt ?? '-' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                ยังไม่มีบทความ
            </div>
        @endif
    </div>
</section>