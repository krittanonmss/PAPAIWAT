<x-layouts.admin :title="'หน้า'">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        จัดการหน้าเว็บไซต์
                    </p>
                    <h1 class="text-2xl font-bold text-white">จัดการหน้าเว็บไซต์</h1>
                    <p class="mt-1 text-sm text-slate-400">
                        จัดการหน้าเว็บไซต์ เทมเพลต SEO และสถานะการเผยแพร่
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.pages.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    <span class="text-lg leading-none">+</span>
                    สร้างหน้าเว็บไซต์
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการหน้าเว็บไซต์</h2>
                    <p class="text-sm text-slate-400">
                        รายการหน้าเว็บไซต์ทั้งหมดในระบบ
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">หน้า</th>
                            <th class="px-4 py-3 text-left">Slug</th>
                            <th class="px-4 py-3 text-left">เทมเพลต</th>
                            <th class="px-4 py-3 text-left">ประเภท</th>
                            <th class="px-4 py-3 text-left">หน้าแรก</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-right">การจัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse($pages as $page)
                            <tr class="transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white shadow-lg shadow-indigo-950/30">
                                            {{ strtoupper(substr($page->title, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">
                                                {{ $page->title }}
                                            </p>

                                            @if($page->excerpt)
                                                <p class="truncate text-xs text-slate-400">
                                                    {{ $page->excerpt }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ $page->slug }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $page->template?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                        {{ $page->page_type }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @if($page->is_homepage)
                                        <span class="inline-flex rounded-full border border-indigo-400/20 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-300">
                                            หน้าแรก
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    @if($page->status === 'published')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                            เผยแพร่
                                        </span>
                                    @elseif($page->status === 'draft')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-yellow-400/20 bg-yellow-500/10 px-3 py-1 text-xs font-medium text-yellow-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-300"></span>
                                            ฉบับร่าง
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                            เก็บถาวร
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">

                                        <a
                                            href="{{ route('admin.content.pages.show', $page) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            ดู
                                        </a>

                                        <a
                                            href="{{ route('admin.content.pages.edit', $page) }}"
                                            class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20 hover:text-blue-200"
                                        >
                                            แก้ไข
                                        </a>

                                        <form
                                            action="{{ route('admin.content.pages.destroy', $page) }}"
                                            method="POST"
                                            onsubmit="return confirm('ยืนยันการลบหน้านี้?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-xl border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs font-medium text-rose-300 transition hover:bg-rose-500/20 hover:text-rose-200"
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
                                    <p class="text-base font-medium text-slate-300">ยังไม่มีหน้าเว็บไซต์</p>
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
                <div class="border-t border-white/10 px-5 py-3">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
