<x-layouts.admin :title="'Pages'">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Page Management</h1>
                <p class="text-sm text-slate-500">
                    จัดการหน้าเว็บไซต์ เทมเพลต SEO และสถานะการเผยแพร่
                </p>
            </div>

            <a
                href="{{ route('admin.content.pages.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create Page
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">Pages</h2>
                <p class="mt-1 text-sm text-slate-500">
                    รายการหน้าเว็บไซต์ทั้งหมดในระบบ
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Page</th>
                            <th class="px-5 py-3 font-semibold">Slug</th>
                            <th class="px-5 py-3 font-semibold">Template</th>
                            <th class="px-5 py-3 font-semibold">Type</th>
                            <th class="px-5 py-3 font-semibold">Homepage</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($pages as $page)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-900">
                                        {{ $page->title }}
                                    </div>

                                    @if($page->excerpt)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $page->excerpt }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                        {{ $page->slug }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $page->template?->name ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $page->page_type }}
                                </td>

                                <td class="px-5 py-4">
                                    @if($page->is_homepage)
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                            Homepage
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
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
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a
                                        href="{{ route('admin.content.pages.show', $page) }}"
                                        class="text-sm font-medium text-slate-700 hover:text-slate-950"
                                    >
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center">
                                    <div class="text-sm font-medium text-slate-700">
                                        ยังไม่มีหน้าเว็บไซต์
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">
                                        เริ่มสร้างหน้าแรกหรือหน้าเนื้อหาใหม่ได้เลย
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pages->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>