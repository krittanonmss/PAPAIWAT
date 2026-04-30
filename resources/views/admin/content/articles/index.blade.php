<x-layouts.admin title="จัดการบทความ" header="จัดการบทความ">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Article Management
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการบทความ</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการเนื้อหาบทความ หมวดหมู่ แท็ก SEO และสถานะการเผยแพร่
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.articles.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    เพิ่มบทความใหม่
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.content.articles.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                    <div class="lg:col-span-4">
                        <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาบทความ</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="ชื่อเรื่อง / slug / คำโปรย / รายละเอียด"
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
                            <option value="draft" class="bg-slate-900" @selected(request('status') === 'draft')>ฉบับร่าง</option>
                            <option value="published" class="bg-slate-900" @selected(request('status') === 'published')>เผยแพร่แล้ว</option>
                            <option value="archived" class="bg-slate-900" @selected(request('status') === 'archived')>เก็บถาวร</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="body_format" class="mb-1.5 block text-xs font-medium text-slate-400">รูปแบบเนื้อหา</label>
                        <select
                            id="body_format"
                            name="body_format"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="" class="bg-slate-900">ทุกรูปแบบ</option>
                            <option value="markdown" class="bg-slate-900" @selected(request('body_format') === 'markdown')>Markdown</option>
                            <option value="html" class="bg-slate-900" @selected(request('body_format') === 'html')>HTML</option>
                            <option value="editorjs" class="bg-slate-900" @selected(request('body_format') === 'editorjs')>EditorJS</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="author_name" class="mb-1.5 block text-xs font-medium text-slate-400">ผู้เขียน</label>
                        <input
                            type="text"
                            id="author_name"
                            name="author_name"
                            value="{{ request('author_name') }}"
                            placeholder="ชื่อผู้เขียน"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                        <button
                            type="submit"
                            class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            ค้นหา
                        </button>

                        <a
                            href="{{ route('admin.content.articles.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                        >
                            ล้าง
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-6">
                    <div>
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

                    <div>
                        <label for="tag_id" class="mb-1.5 block text-xs font-medium text-slate-400">แท็ก</label>
                        <select
                            id="tag_id"
                            name="tag_id"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="" class="bg-slate-900">ทุกแท็ก</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}" class="bg-slate-900" @selected((string) request('tag_id') === (string) $tag->id)>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="allow_comments" class="mb-1.5 block text-xs font-medium text-slate-400">ความคิดเห็น</label>
                        <select
                            id="allow_comments"
                            name="allow_comments"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="" class="bg-slate-900">ทั้งหมด</option>
                            <option value="1" class="bg-slate-900" @selected(request('allow_comments') === '1')>เปิดความคิดเห็น</option>
                            <option value="0" class="bg-slate-900" @selected(request('allow_comments') === '0')>ปิดความคิดเห็น</option>
                        </select>
                    </div>

                    <div>
                        <label for="show_on_homepage" class="mb-1.5 block text-xs font-medium text-slate-400">หน้าแรก</label>
                        <select
                            id="show_on_homepage"
                            name="show_on_homepage"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="" class="bg-slate-900">ทั้งหมด</option>
                            <option value="1" class="bg-slate-900" @selected(request('show_on_homepage') === '1')>แสดงหน้าแรก</option>
                            <option value="0" class="bg-slate-900" @selected(request('show_on_homepage') === '0')>ไม่แสดงหน้าแรก</option>
                        </select>
                    </div>

                    <div>
                        <label for="is_featured" class="mb-1.5 block text-xs font-medium text-slate-400">บทความแนะนำ</label>
                        <select
                            id="is_featured"
                            name="is_featured"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="" class="bg-slate-900">ทั้งหมด</option>
                            <option value="1" class="bg-slate-900" @selected(request('is_featured') === '1')>แนะนำ</option>
                            <option value="0" class="bg-slate-900" @selected(request('is_featured') === '0')>ไม่แนะนำ</option>
                        </select>
                    </div>

                    <div>
                        <label for="is_popular" class="mb-1.5 block text-xs font-medium text-slate-400">ยอดนิยม</label>
                        <select
                            id="is_popular"
                            name="is_popular"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="" class="bg-slate-900">ทั้งหมด</option>
                            <option value="1" class="bg-slate-900" @selected(request('is_popular') === '1')>ยอดนิยม</option>
                            <option value="0" class="bg-slate-900" @selected(request('is_popular') === '0')>ไม่ยอดนิยม</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการบทความ</h2>
                    <p class="text-sm text-slate-400">
                        แสดงข้อมูลบทความ สถานะ หมวดหมู่ แท็ก และสถิติการใช้งาน
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full border border-white/10 bg-slate-950/30 px-3 py-1 text-xs font-medium text-slate-400">
                    ทั้งหมด {{ $articles->total() }} รายการ
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">บทความ</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">ผู้เขียน</th>
                            <th class="px-4 py-3 text-left">หมวดหมู่</th>
                            <th class="px-4 py-3 text-left">แท็ก</th>
                            <th class="px-4 py-3 text-left">สถิติ</th>
                            <th class="px-4 py-3 text-left">เผยแพร่เมื่อ</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($articles as $article)
                            @php
                                $content = $article->content;
                                $coverMedia = $content?->mediaUsages?->firstWhere('role_key', 'cover')?->media;
                            @endphp

                            <tr class="align-top transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 shrink-0 overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 shadow-lg shadow-slate-950/30">
                                            @if ($coverMedia)
                                                <img
                                                    src="{{ asset('storage/' . $coverMedia->path) }}"
                                                    alt="{{ $coverMedia->alt_text ?: ($content?->title ?? 'Article cover') }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white">
                                                    {{ strtoupper(substr($content?->title ?? '-', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0 space-y-1">
                                            <p class="truncate font-semibold text-white">
                                                {{ $content?->title ?? '-' }}
                                            </p>

                                            <p class="truncate text-xs text-slate-400">
                                                Slug: {{ $content?->slug ?? '-' }}
                                            </p>

                                            @if ($article->title_en)
                                                <p class="truncate text-xs text-slate-500">
                                                    EN: {{ $article->title_en }}
                                                </p>
                                            @endif

                                            <div class="flex flex-wrap gap-2 pt-1">
                                                @if ($content?->is_featured)
                                                    <span class="inline-flex rounded-full border border-yellow-400/20 bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-300">
                                                        แนะนำ
                                                    </span>
                                                @endif

                                                @if ($content?->is_popular)
                                                    <span class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                                                        ยอดนิยม
                                                    </span>
                                                @endif

                                                @if ($article->show_on_homepage)
                                                    <span class="inline-flex rounded-full border border-violet-400/20 bg-violet-500/10 px-3 py-1 text-xs font-medium text-violet-300">
                                                        หน้าแรก
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    @switch($content?->status)
                                        @case('draft')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-yellow-400/20 bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-yellow-300"></span>
                                                ฉบับร่าง
                                            </span>
                                            @break

                                        @case('published')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                                เผยแพร่แล้ว
                                            </span>
                                            @break

                                        @case('archived')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                                เก็บถาวร
                                            </span>
                                            @break

                                        @default
                                            <span class="inline-flex rounded-full border border-red-400/20 bg-red-500/10 px-3 py-1 text-xs font-medium text-red-300">
                                                ไม่ทราบสถานะ
                                            </span>
                                    @endswitch
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-slate-300">{{ $article->author_name ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $article->reading_time_minutes ? $article->reading_time_minutes . ' นาที' : '-' }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex max-w-xs flex-wrap gap-2">
                                        @forelse ($content?->categories ?? [] as $category)
                                            <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                                {{ $category->name }}
                                            </span>
                                        @empty
                                            <span class="text-slate-500">-</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex max-w-xs flex-wrap gap-2">
                                        @forelse ($article->tags as $tag)
                                            <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                                {{ $tag->name }}
                                            </span>
                                        @empty
                                            <span class="text-slate-500">-</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    <div class="grid gap-1 text-xs">
                                        <div>เข้าชม: {{ $article->stat?->view_count ?? 0 }}</div>
                                        <div>ถูกใจ: {{ $article->stat?->like_count ?? 0 }}</div>
                                        <div>บันทึก: {{ $article->stat?->bookmark_count ?? 0 }}</div>
                                        <div>แชร์: {{ $article->stat?->share_count ?? 0 }}</div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.content.articles.show', $article) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            ดู
                                        </a>

                                        <a
                                            href="{{ route('admin.content.articles.edit', $article) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            แก้ไข
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.content.articles.destroy', $article) }}"
                                            onsubmit="return confirm('ยืนยันการลบบทความนี้หรือไม่?');"
                                        >
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
                                <td colspan="8" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ไม่พบบทความ</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        ยังไม่มีบทความในระบบ หรือไม่มีข้อมูลที่ตรงกับตัวกรอง
                                    </p>

                                    <a
                                        href="{{ route('admin.content.articles.create') }}"
                                        class="mt-4 inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                                    >
                                        <span class="text-lg leading-none">+</span>
                                        เพิ่มบทความใหม่
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($articles->hasPages())
                <div class="border-t border-white/10 px-5 py-3">
                    {{ $articles->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.admin>