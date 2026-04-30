<section class="relative py-16">
    <div class="mx-auto max-w-6xl px-4">

        {{-- Header --}}
        <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-white">
                    {{ $content['title'] ?? 'รายการวัด' }}
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
        @if ($data && $data->isNotEmpty())
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($data as $temple)
                    @php
                        $contentModel = $temple->content;
                        $address = $temple->address;
                        $stat = $temple->stat;

                        $cover = $contentModel?->mediaUsages?->firstWhere('role_key', 'cover');
                        $imageUrl = $cover?->media?->path
                            ? (filter_var($cover->media->path, FILTER_VALIDATE_URL)
                                ? $cover->media->path
                                : \Illuminate\Support\Facades\Storage::url($cover->media->path))
                            : null;

                        $openingHour = $temple->openingHours?->firstWhere('is_closed', false);

                        $openTime = $openingHour?->open_time
                            ? \Carbon\Carbon::parse($openingHour->open_time)->format('H:i')
                            : null;

                        $closeTime = $openingHour?->close_time
                            ? \Carbon\Carbon::parse($openingHour->close_time)->format('H:i')
                            : null;

                        $fee = $temple->fees?->firstWhere('is_active', true);
                        $highlight = $temple->highlights?->first();
                    @endphp

                    <a
                        href="{{ route('temples.show', $temple) }}"
                        class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:border-white/20"
                    >
                        {{-- Image --}}
                        <div class="relative h-52 w-full overflow-hidden bg-slate-900">
                            @if ($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $contentModel?->title }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-full items-center justify-center text-xs text-slate-500">
                                    No Image
                                </div>
                            @endif

                            @if ($contentModel?->is_featured)
                                <span class="absolute left-3 top-3 rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-medium text-white">
                                    แนะนำ
                                </span>
                            @endif

                            @if ($contentModel?->is_popular)
                                <span class="absolute right-3 top-3 rounded-full bg-blue-500 px-2 py-0.5 text-[10px] font-medium text-white">
                                    ยอดนิยม
                                </span>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="space-y-3 p-5">
                            <div>
                                <h3 class="line-clamp-1 text-base font-semibold text-white">
                                    {{ $contentModel?->title ?? '-' }}
                                </h3>

                                <p class="mt-1 line-clamp-2 text-sm text-slate-400">
                                    {{ $contentModel?->excerpt ?? $highlight?->description ?? '-' }}
                                </p>
                            </div>

                            <div class="space-y-1.5 text-xs text-slate-400">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="line-clamp-1">
                                        {{ $address?->province ?? '-' }}
                                    </span>

                                    @if ($stat)
                                        <span class="shrink-0 text-amber-300">
                                            ⭐ {{ number_format($stat->score, 1) }}
                                        </span>
                                    @endif
                                </div>

                                @if ($temple->temple_type || $temple->sect)
                                    <div class="line-clamp-1 text-slate-500">
                                        {{ $temple->temple_type ?: '-' }}
                                        @if ($temple->sect)
                                            · {{ $temple->sect }}
                                        @endif
                                    </div>
                                @endif

                                @if ($openTime || $closeTime)
                                    <div class="text-slate-500">
                                        เปิด {{ $openTime ?? '--:--' }} - {{ $closeTime ?? '--:--' }}
                                    </div>
                                @endif

                                @if ($fee)
                                    <div class="text-slate-500">
                                        ค่าเข้า:
                                        {{ $fee->amount !== null ? number_format($fee->amount, 0) . ' ' . ($fee->currency ?: 'THB') : 'ฟรี' }}
                                    </div>
                                @endif
                            </div>

                            @if ($stat)
                                <div class="flex items-center justify-between border-t border-white/10 pt-3 text-[11px] text-slate-500">
                                    <span>รีวิว {{ number_format($stat->review_count) }}</span>
                                    <span>ถูกใจ {{ number_format($stat->favorite_count) }}</span>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                ยังไม่มีข้อมูลวัดสำหรับ section นี้
            </div>
        @endif
    </div>
</section>