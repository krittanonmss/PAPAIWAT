<x-layouts.admin :title="'Templates'">
    <div class="space-y-6 text-white">
        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-300">CMS Layout</p>
                    <h1 class="mt-1 text-2xl font-bold text-white">Template Management</h1>
                    <p class="mt-2 text-sm text-slate-400">
                        จัดการรูปแบบการ render หน้าเว็บไซต์และ layout หลักของ CMS
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.templates.create') }}"
                    class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    + Create Template
                </a>
            </div>
        </div>

        {{-- Template List --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-5">
                <div class="flex flex-col gap-1">
                    <h2 class="text-base font-semibold text-white">Templates</h2>
                    <p class="text-sm text-slate-400">
                        รายการ Template ทั้งหมดในระบบ
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-950/50 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Template</th>
                            <th class="px-6 py-4 font-semibold">Key</th>
                            <th class="px-6 py-4 font-semibold">View Path</th>
                            <th class="px-6 py-4 font-semibold">Pages</th>
                            <th class="px-6 py-4 font-semibold">Default</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 text-right font-semibold">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10">
                        @forelse($templates as $template)
                            <tr class="transition hover:bg-white/[0.05]">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">
                                        {{ $template->name }}
                                    </div>

                                    @if($template->description)
                                        <p class="mt-1 max-w-md text-xs leading-5 text-slate-400">
                                            {{ $template->description }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-xl border border-white/10 bg-slate-950/40 px-2.5 py-1 text-xs font-medium text-slate-300">
                                        {{ $template->key }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <code class="inline-flex rounded-xl border border-white/10 bg-slate-950/40 px-2.5 py-1 text-xs text-blue-300">
                                        {{ $template->view_path }}
                                    </code>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-2.5 text-xs font-semibold text-slate-200">
                                        {{ $template->pages_count }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @if($template->is_default)
                                        <span class="inline-flex rounded-full border border-indigo-400/20 bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-300">
                                            Default
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($template->status === 'active')
                                        <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full border border-white/10 bg-slate-800/60 px-2.5 py-1 text-xs font-medium text-slate-400">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('admin.content.templates.show', $template) }}"
                                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-xs font-medium text-slate-200 transition hover:border-blue-400/30 hover:bg-blue-500/10 hover:text-blue-300"
                                    >
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <div class="mx-auto max-w-sm rounded-3xl border border-white/10 bg-slate-950/30 p-6">
                                        <div class="text-sm font-semibold text-white">
                                            ยังไม่มี Template
                                        </div>
                                        <p class="mt-2 text-sm text-slate-400">
                                            เริ่มสร้าง Template สำหรับหน้าเว็บไซต์ได้เลย
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($templates->hasPages())
                <div class="border-t border-white/10 px-6 py-4">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>