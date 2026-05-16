<x-layouts.admin :title="$page->title">
    <div class="space-y-6 text-white">

        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        Page Detail
                    </div>

                    <h1 class="text-2xl font-bold text-white">{{ $page->title }}</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        รายละเอียดหน้าเว็บไซต์และ block ภายในหน้า
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('admin.content.pages.sections.create', $page) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
                    >
                        เพิ่ม Block
                    </a>

                    <a
                        href="{{ route('admin.content.pages.edit', $page) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-amber-950/40 transition hover:opacity-90"
                    >
                        แก้ไข
                    </a>

                    <a
                        href="{{ route('admin.content.pages.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        กลับไปรายการหน้า
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-emerald-950/20">
                {{ session('success') }}
            </div>
        @endif

        @php
            $statusClass = match($page->status) {
                'published' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300',
                'draft' => 'border-amber-400/20 bg-amber-500/10 text-amber-300',
                'archived' => 'border-slate-400/20 bg-slate-500/10 text-slate-300',
                default => 'border-white/10 bg-white/[0.04] text-slate-300',
            };
        @endphp

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Main Content --}}
            <div class="space-y-6 xl:col-span-2">
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Content Summary</h2>
                    </div>

                    <div class="space-y-5 p-6">
                        <div>
                            <p class="mb-1 text-xs font-medium text-slate-500">Excerpt</p>
                            <p class="text-sm leading-6 text-slate-300">{{ $page->excerpt ?: '-' }}</p>
                        </div>

                        <div>
                            <p class="mb-1 text-xs font-medium text-slate-500">คำอธิบาย</p>
                            <p class="whitespace-pre-line text-sm leading-6 text-slate-300">{{ $page->description ?: '-' }}</p>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="flex flex-col gap-4 border-b border-white/10 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-white">Page Builder</h2>
                            <p class="mt-1 text-sm text-slate-400">
                                จัดการ block ที่ประกอบเป็นหน้าเว็บ
                            </p>
                        </div>

                        <a
                            href="{{ route('admin.content.pages.sections.create', $page) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                        >
                            Add
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-950/50 text-xs uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Block</th>
                                    <th class="px-6 py-4 font-semibold">Component</th>
                                    <th class="px-6 py-4 font-semibold">Order</th>
                                    <th class="px-6 py-4 font-semibold">Visible</th>
                                    <th class="px-6 py-4 font-semibold">สถานะ</th>
                                    <th class="px-6 py-4 text-right font-semibold">การจัดการ</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/10">
                                @forelse ($page->sections as $section)
                                    <tr class="{{ $section->is_visible && $section->status === 'active' ? 'transition hover:bg-white/[0.05]' : 'bg-slate-950/30 text-slate-500' }}">
                                        <td class="px-6 py-4">
                                            <div class="font-medium {{ $section->is_visible && $section->status === 'active' ? 'text-white' : 'text-slate-500' }}">
                                                {{ $section->name }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $section->section_key }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-xl border border-white/10 bg-slate-950/40 px-2.5 py-1 text-xs font-medium text-slate-300">
                                                {{ $section->component_key }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-2.5 text-xs font-semibold text-slate-200">
                                                {{ $section->sort_order }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            @if ($section->is_visible)
                                                <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-300">
                                                    Visible
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full border border-slate-400/20 bg-slate-500/10 px-2.5 py-1 text-xs font-medium text-slate-400">
                                                    Hidden
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            @if ($section->status === 'active')
                                                <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-300">
                                                    เปิดใช้งาน
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full border border-slate-400/20 bg-slate-500/10 px-2.5 py-1 text-xs font-medium text-slate-400">
                                                    ปิดใช้งาน
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <a
                                                    href="{{ route('admin.content.pages.sections.edit', [$page, $section]) }}"
                                                    class="inline-flex items-center justify-center rounded-lg border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.content.pages.sections.destroy', [$page, $section]) }}"
                                                    onsubmit="return confirm('ยืนยันการลบ block นี้?')"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center justify-center rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs font-medium text-rose-200 transition hover:bg-rose-500/20"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-14 text-center">
                                            <div class="mx-auto max-w-sm rounded-3xl border border-white/10 bg-slate-950/30 p-6">
                                                <div class="text-sm font-semibold text-white">
                                                    ยังไม่มี block ในหน้านี้
                                                </div>
                                                <p class="mt-2 text-sm text-slate-400">
                                                    เริ่มเพิ่ม block เพื่อจัดหน้าแบบ page builder
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Page Detail</h2>
                    </div>

                    <div class="divide-y divide-white/10">
                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">หัวข้อ</p>
                            <p class="mt-0.5 break-words text-sm font-medium text-slate-200">{{ $page->title }}</p>
                        </div>

                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">Slug</p>
                            <div class="mt-1">
                                <span class="inline-flex rounded-xl border border-white/10 bg-slate-950/40 px-2.5 py-1 text-xs font-medium text-slate-300">
                                    {{ $page->slug }}
                                </span>
                            </div>
                        </div>

                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">เทมเพลต</p>
                            <p class="mt-0.5 text-sm text-slate-300">{{ $page->template?->name ?? '-' }}</p>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Page Type</span>
                            <span class="text-sm font-medium text-slate-200">{{ $page->page_type }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">ลำดับ</span>
                            <span class="text-sm font-medium text-slate-200">{{ $page->sort_order }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Homepage</span>
                            @if ($page->is_homepage)
                                <span class="inline-flex rounded-full border border-indigo-400/20 bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-300">
                                    Homepage
                                </span>
                            @else
                                <span class="text-sm text-slate-500">No</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">สถานะ</span>
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($page->status) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Published At</span>
                            <span class="text-right text-sm text-slate-300">
                                {{ $page->published_at?->format('d/m/Y H:i') ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Unpublished At</span>
                            <span class="text-right text-sm text-slate-300">
                                {{ $page->unpublished_at?->format('d/m/Y H:i') ?? '-' }}
                            </span>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">SEO Summary</h2>
                    </div>

                    <div class="space-y-4 p-6">
                        <div>
                            <p class="text-xs font-medium text-slate-500">Meta Title</p>
                            <p class="mt-0.5 text-sm text-slate-200">{{ $page->meta_title ?: '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-slate-500">Meta คำอธิบาย</p>
                            <p class="mt-0.5 text-sm leading-6 text-slate-300">{{ $page->meta_description ?: '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-slate-500">Canonical URL</p>
                            <p class="mt-0.5 break-all text-sm text-slate-300">{{ $page->canonical_url ?: '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-slate-500">OG Image</p>
                            <p class="mt-0.5 text-sm text-slate-300">
                                {{ $page->ogImage?->title ?: ($page->ogImage?->original_filename ?? '-') }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-rose-400/20 bg-rose-500/10 shadow-xl shadow-rose-950/20 backdrop-blur">
                    <div class="border-b border-rose-400/20 px-6 py-4">
                        <h2 class="text-base font-semibold text-rose-200">โซนอันตราย</h2>
                    </div>

                    <div class="p-6">
                        <p class="mb-4 text-sm text-rose-200/80">
                            ลบหน้าเว็บไซต์นี้ออกจากระบบ
                        </p>

                        <form
                            method="POST"
                            action="{{ route('admin.content.pages.destroy', $page) }}"
                            onsubmit="return confirm('ยืนยันการลบหน้านี้?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-2.5 text-sm font-medium text-rose-200 transition hover:bg-rose-500/20"
                            >
                                Delete Page
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.admin>
