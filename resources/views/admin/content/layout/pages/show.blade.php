<x-layouts.admin :title="$page->title">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $page->title }}</h1>
                <p class="text-sm text-slate-500">
                    รายละเอียดหน้าเว็บไซต์และ sections ภายในหน้า
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('admin.content.pages.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Back
                </a>

                <a
                    href="{{ route('admin.content.pages.edit', $page) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Edit
                </a>

                <a
                    href="{{ route('admin.content.pages.sections.create', $page) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Add Section
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Page Detail</h2>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Title</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $page->title }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Slug</dt>
                            <dd class="mt-1">
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                    {{ $page->slug }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Template</dt>
                            <dd class="mt-1 text-slate-900">{{ $page->template?->name ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Page Type</dt>
                            <dd class="mt-1 text-slate-900">{{ $page->page_type }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Sort Order</dt>
                            <dd class="mt-1 text-slate-900">{{ $page->sort_order }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Homepage</dt>
                            <dd class="mt-1">
                                @if($page->is_homepage)
                                    <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                        Homepage
                                    </span>
                                @else
                                    <span class="text-slate-500">No</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Status</dt>
                            <dd class="mt-1">
                                @if($page->status === 'published')
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Published
                                    </span>
                                @elseif($page->status === 'draft')
                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                        Archived
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Published At</dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $page->published_at?->format('d/m/Y H:i') ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Unpublished At</dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $page->unpublished_at?->format('d/m/Y H:i') ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">SEO Summary</h2>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Meta Title</dt>
                            <dd class="mt-1 text-slate-900">{{ $page->meta_title ?: '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Meta Description</dt>
                            <dd class="mt-1 text-slate-900">{{ $page->meta_description ?: '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Canonical URL</dt>
                            <dd class="mt-1 break-all text-slate-900">{{ $page->canonical_url ?: '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">OG Image</dt>
                            <dd class="mt-1 text-slate-900">
                                {{ $page->ogImage?->title ?: ($page->ogImage?->original_filename ?? '-') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
                    <div>
                        <h2 class="text-base font-semibold text-red-900">Danger Zone</h2>
                        <p class="mt-1 text-sm text-red-700">
                            ลบหน้าเว็บไซต์นี้ออกจากระบบ
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.content.pages.destroy', $page) }}"
                        onsubmit="return confirm('ยืนยันการลบหน้านี้?')"
                        class="mt-4"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700"
                        >
                            Delete Page
                        </button>
                    </form>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Content Summary</h2>

                    <div class="mt-5 space-y-4 text-sm">
                        <div>
                            <div class="text-slate-500">Excerpt</div>
                            <p class="mt-1 text-slate-900">{{ $page->excerpt ?: '-' }}</p>
                        </div>

                        <div>
                            <div class="text-slate-500">Description</div>
                            <p class="mt-1 whitespace-pre-line text-slate-900">{{ $page->description ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Page Sections</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                โครงสร้าง sections ที่ใช้ประกอบหน้านี้
                            </p>
                        </div>

                        <a
                            href="{{ route('admin.content.pages.sections.create', $page) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800"
                        >
                            Add
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Section</th>
                                    <th class="px-5 py-3 font-semibold">Component</th>
                                    <th class="px-5 py-3 font-semibold">Order</th>
                                    <th class="px-5 py-3 font-semibold">Visible</th>
                                    <th class="px-5 py-3 font-semibold">Status</th>
                                    <th class="px-5 py-3 text-right font-semibold">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @forelse($page->sections as $section)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-slate-900">
                                                {{ $section->name }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $section->section_key }}
                                            </div>
                                        </td>

                                        <td class="px-5 py-4">
                                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                {{ $section->component_key }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-4 text-slate-700">
                                            {{ $section->sort_order }}
                                        </td>

                                        <td class="px-5 py-4">
                                            @if($section->is_visible)
                                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                                    Visible
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                                    Hidden
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4">
                                            @if($section->status === 'active')
                                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4 text-right">
                                            <a
                                                href="{{ route('admin.content.pages.sections.edit', [$page, $section]) }}"
                                                class="text-sm font-medium text-slate-700 hover:text-slate-950"
                                            >
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center">
                                            <div class="text-sm font-medium text-slate-700">
                                                ยังไม่มี section
                                            </div>
                                            <p class="mt-1 text-sm text-slate-500">
                                                เพิ่ม section แรกเพื่อเริ่มประกอบหน้าเว็บไซต์
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>