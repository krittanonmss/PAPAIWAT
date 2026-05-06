@php
    $content = $section->content_data ?? [];
    $items = $section->items ?? collect();
    $filters = $section->filters ?? [];
    $totalItems = method_exists($items, 'total') ? $items->total() : collect($items)->count();
    $activeSearch = request('search');
    $activeProvince = request('province');
    $activeCategory = request('category');
    $activeSort = request('sort');
    $categories = collect($filters['categories'] ?? []);
    $provinces = collect($filters['provinces'] ?? []);
    $hasActiveFilters = $activeSearch || $activeProvince || $activeCategory || $activeSort;
@endphp

<section class="bg-slate-950 px-4 py-16 text-white">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 text-center">
            @if(!empty($content['eyebrow']))
                <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
            @endif
            <h1 class="mt-3 text-4xl font-bold md:text-6xl">{{ $content['title'] ?? 'รวมวัดทั่วไทย' }}</h1>
            @if(!empty($content['subtitle']))
                <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-400">{{ $content['subtitle'] }}</p>
            @endif
        </div>

        <form method="GET" class="mb-8 rounded-3xl border border-white/10 bg-slate-900/75 p-5 shadow-2xl shadow-slate-950/50 backdrop-blur-xl">
            <input type="search" name="search" value="{{ $activeSearch }}" placeholder="ค้นหาวัด, จังหวัด, หรือสิ่งที่คุณต้องการ..." class="w-full rounded-2xl border border-white/10 bg-white/10 py-4 px-5 text-sm text-white placeholder:text-slate-400 outline-none transition focus:border-blue-400/50">

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <select name="province" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-slate-200 outline-none">
                    <option value="">ทุกจังหวัด</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province }}" @selected($activeProvince === $province)>{{ $province }}</option>
                    @endforeach
                </select>

                <select name="category" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-slate-200 outline-none">
                    <option value="">ทุกหมวดหมู่</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug ?? $category->id }}" @selected((string) $activeCategory === (string) ($category->slug ?? $category->id))>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="sort" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-slate-200 outline-none">
                    <option value="">เรียงตามระบบ</option>
                    <option value="popular" @selected($activeSort === 'popular')>คะแนนสูงสุด</option>
                    <option value="rating" @selected($activeSort === 'rating')>รีวิวดีที่สุด</option>
                    <option value="latest" @selected($activeSort === 'latest')>ล่าสุด</option>
                </select>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                <span class="rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs text-blue-200">ทั้งหมด {{ number_format($totalItems) }} วัด</span>
                <div class="flex gap-2">
                    @if ($hasActiveFilters)
                        <a href="{{ url()->current() }}" class="rounded-2xl border border-blue-300/20 bg-blue-300/10 px-4 py-2.5 text-sm font-medium text-blue-100">ล้างตัวกรอง</a>
                    @endif
                    <button class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">ค้นหา</button>
                </div>
            </div>
        </form>

        <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-4">
            @forelse ($items as $temple)
                @php
                    $templeContent = $temple->relationLoaded('content') ? $temple->content : null;
                    $address = $temple->relationLoaded('address') ? $temple->address : null;
                    $mediaUsages = ($templeContent && $templeContent->relationLoaded('mediaUsages')) ? $templeContent->mediaUsages : collect();
                    $cover = $mediaUsages->firstWhere('role_key', 'cover');
                    $coverMedia = ($cover && $cover->relationLoaded('media')) ? $cover->media : null;
                    $path = $coverMedia?->path;
                    $imageUrl = $path ? (filter_var($path, FILTER_VALIDATE_URL) ? $path : \Illuminate\Support\Facades\Storage::url($path)) : null;
                @endphp
                <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl shadow-slate-950/30 transition hover:-translate-y-1 hover:border-blue-300/40">
                    <a href="{{ route('temples.show', $temple) }}">
                        <div class="h-64 bg-slate-900">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $templeContent?->title ?? 'Temple image' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-slate-500">No Image</div>
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-xs text-blue-300">{{ $address?->province ?? 'ไม่ระบุจังหวัด' }}</p>
                            <h2 class="mt-2 line-clamp-2 text-2xl font-medium">{{ $templeContent?->title ?? '-' }}</h2>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $templeContent?->excerpt ?? 'ยังไม่มีคำโปรย' }}</p>
                        </div>
                    </a>
                </article>
            @empty
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 md:col-span-2 xl:col-span-4">ยังไม่มีวัด</div>
            @endforelse
        </div>

        @if (method_exists($items, 'links') && $items->hasPages())
            <div class="mt-10 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-xl shadow-slate-950/30">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>
