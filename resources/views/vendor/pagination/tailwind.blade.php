{{-- แก้ pagination ให้เป็น dark theme แบบจริงจัง (ไม่ใช่ hack css) --}}
{{-- ไปสร้างไฟล์นี้: resources/views/vendor/pagination/tailwind.blade.php --}}

@if ($paginator->hasPages())
    <nav role="navigation" class="flex items-center justify-between">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-500">
                    ก่อนหน้า
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="relative inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-300 hover:bg-white/5">
                    ก่อนหน้า
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="relative ml-3 inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-300 hover:bg-white/5">
                    ถัดไป
                </a>
            @else
                <span class="relative ml-3 inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm text-slate-500">
                    ถัดไป
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-end">
            <div>
                <span class="relative z-0 inline-flex gap-1 rounded-xl bg-white/[0.03] p-1">

                    {{-- Previous --}}
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex items-center rounded-lg px-3 py-2 text-slate-500">
                            ‹
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}"
                           class="inline-flex items-center rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5">
                            ‹
                        </a>
                    @endif

                    {{-- Pages --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="px-3 py-2 text-slate-500">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="inline-flex items-center rounded-lg bg-blue-600/20 px-3 py-2 text-sm font-medium text-blue-300">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                       class="inline-flex items-center rounded-lg px-3 py-2 text-sm text-slate-300 hover:bg-white/5">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}"
                           class="inline-flex items-center rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5">
                            ›
                        </a>
                    @else
                        <span class="inline-flex items-center rounded-lg px-3 py-2 text-slate-500">
                            ›
                        </span>
                    @endif

                </span>
            </div>
        </div>
    </nav>
@endif