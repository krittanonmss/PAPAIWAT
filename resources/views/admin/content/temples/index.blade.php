<x-layouts.admin :title="$title" header="จัดการข้อมูลวัด">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Temple Management
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการข้อมูลวัด</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการข้อมูลวัด หมวดหมู่ สถานะการเผยแพร่ และข้อมูลสำหรับแสดงผลหน้าเว็บไซต์
                    </p>
                </div>

                <a
                    href="{{ route('admin.temples.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    เพิ่มข้อมูลวัด
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.temples.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาข้อมูลวัด</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="ชื่อวัด / slug / รายละเอียด"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="status" class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ทุกสถานะ</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" class="bg-slate-900" @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="category_id" class="mb-1.5 block text-xs font-medium text-slate-400">หมวดหมู่</label>
                    <select
                        id="category_id"
                        name="category_id"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ทุกหมวดหมู่</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" class="bg-slate-900" @selected((string) request('category_id') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="sort" class="mb-1.5 block text-xs font-medium text-slate-400">เรียงลำดับ</label>
                    <select
                        id="sort"
                        name="sort"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ล่าสุด</option>
                        <option value="popular" class="bg-slate-900" @selected(request('sort') === 'popular')>ยอดนิยม</option>
                        <option value="oldest" class="bg-slate-900" @selected(request('sort') === 'oldest')>เก่าสุด</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                    <button
                        type="submit"
                        class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.temples.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                    >
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการข้อมูลวัด</h2>
                    <p class="text-sm text-slate-400">
                        จำนวน {{ $temples->total() }} รายการ
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">ข้อมูลวัด</th>
                            <th class="px-4 py-3 text-left">หมวดหมู่หลัก</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">สถิติ</th>
                            <th class="px-4 py-3 text-left">แนะนำ</th>
                            <th class="px-4 py-3 text-left">เผยแพร่เมื่อ</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($temples as $temple)
                            @php
                                $content = $temple->content;
                                $primaryCategory = $content?->categories?->firstWhere('pivot.is_primary', true);
                                $coverMedia = $content?->mediaUsages?->firstWhere('role_key', 'cover')?->media;
                                $stat = $temple->stat;
                                $viewCount = (int) data_get($stat, 'view_count', 0);
                                $reviewCount = (int) data_get($stat, 'review_count', 0);
                                $averageRating = (float) data_get($stat, 'average_rating', 0);
                                $favoriteCount = (int) data_get($stat, 'favorite_count', 0);
                                $shareCount = (int) data_get($stat, 'share_count', 0);
                                $score = (float) data_get($stat, 'score', 0);
                            @endphp

                            <tr class="align-top transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 shrink-0 overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 shadow-lg shadow-slate-950/30">
                                            @if ($coverMedia)
                                                <img
                                                    src="{{ asset('storage/' . $coverMedia->path) }}"
                                                    alt="{{ $coverMedia->alt_text ?: $content?->title }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white">
                                                    {{ strtoupper(substr($content?->title ?? '-', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">
                                                {{ $content?->title ?? '-' }}
                                            </p>

                                            <p class="mt-1 truncate text-xs text-slate-400">
                                                slug: {{ $content?->slug ?? '-' }}
                                            </p>

                                            @if ($temple->address?->province)
                                                <span class="mt-2 inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                                    {{ $temple->address->province }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ $primaryCategory?->name ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $status = $content?->status;
                                    @endphp

                                    @if ($status === 'published')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                            Published
                                        </span>
                                    @elseif ($status === 'draft')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-yellow-400/20 bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-300"></span>
                                            Draft
                                        </span>
                                    @elseif ($status === 'archived')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                            Archived
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    <div class="grid gap-1 text-xs">
                                        <div>เข้าชม: {{ number_format($viewCount) }}</div>
                                        <div>รีวิว: {{ number_format($reviewCount) }} ({{ $averageRating > 0 ? number_format($averageRating, 1) : '-' }})</div>
                                        <div>Fav: {{ number_format($favoriteCount) }}</div>
                                        <div>แชร์: {{ number_format($shareCount) }}</div>
                                        <div>Score: {{ number_format($score, 2) }}</div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($content?->is_featured)
                                        <span class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                                            ใช่
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-400">
                                            ไม่ใช่
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.temples.show', $temple) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            ดู
                                        </a>

                                        <a
                                            href="{{ route('admin.temples.edit', $temple) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            แก้ไข
                                        </a>

                                        <form method="POST" action="{{ route('admin.temples.destroy', $temple) }}" onsubmit="return confirm('ยืนยันการลบข้อมูลวัดนี้?')">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs font-medium text-red-300 transition hover:bg-red-500/10"
                                            >
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ยังไม่มีข้อมูลวัด</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        เริ่มเพิ่มข้อมูลวัดแรกเพื่อใช้แสดงผลบนหน้าเว็บไซต์
                                    </p>

                                    <a
                                        href="{{ route('admin.temples.create') }}"
                                        class="mt-4 inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                                    >
                                        <span class="text-lg leading-none">+</span>
                                        เพิ่มข้อมูลวัด
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($temples->hasPages())
                <div class="border-t border-white/10 px-5 py-3">
                    {{ $temples->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
