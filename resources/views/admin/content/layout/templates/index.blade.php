<x-layouts.admin :title="'Templates'">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Template Management</h1>
                <p class="text-sm text-slate-500">
                    จัดการรูปแบบการ render หน้าเว็บไซต์และ layout หลักของ CMS
                </p>
            </div>

            <a
                href="{{ route('admin.content.templates.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
            >
                Create Template
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">Templates</h2>
                <p class="mt-1 text-sm text-slate-500">
                    รายการ Template ทั้งหมดในระบบ
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Template</th>
                            <th class="px-5 py-3 font-semibold">Key</th>
                            <th class="px-5 py-3 font-semibold">View Path</th>
                            <th class="px-5 py-3 font-semibold">Pages</th>
                            <th class="px-5 py-3 font-semibold">Default</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($templates as $template)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-900">
                                        {{ $template->name }}
                                    </div>

                                    @if($template->description)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $template->description }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                        {{ $template->key }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <code class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs text-slate-700">
                                        {{ $template->view_path }}
                                    </code>
                                </td>

                                <td class="px-5 py-4 text-slate-700">
                                    {{ $template->pages_count }}
                                </td>

                                <td class="px-5 py-4">
                                    @if($template->is_default)
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                            Default
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if($template->status === 'active')
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
                                        href="{{ route('admin.content.templates.show', $template) }}"
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
                                        ยังไม่มี Template
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">
                                        เริ่มสร้าง Template สำหรับหน้าเว็บไซต์ได้เลย
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($templates->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>