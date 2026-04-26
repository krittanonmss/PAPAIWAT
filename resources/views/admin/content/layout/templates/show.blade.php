<x-layouts.admin :title="$template->name">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $template->name }}</h1>
                <p class="text-sm text-slate-500">
                    รายละเอียด Template และการใช้งานกับหน้าเว็บไซต์
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('admin.content.templates.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Back
                </a>

                <a
                    href="{{ route('admin.content.templates.edit', $template) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Edit
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Template Detail</h2>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-500">Name</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $template->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Key</dt>
                            <dd class="mt-1">
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                    {{ $template->key }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">View Path</dt>
                            <dd class="mt-1">
                                <code class="break-all rounded-lg bg-slate-100 px-2.5 py-1 text-xs text-slate-700">
                                    {{ $template->view_path }}
                                </code>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Pages</dt>
                            <dd class="mt-1 text-slate-900">{{ $template->pages_count }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Sort Order</dt>
                            <dd class="mt-1 text-slate-900">{{ $template->sort_order }}</dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Default</dt>
                            <dd class="mt-1">
                                @if($template->is_default)
                                    <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                        Default
                                    </span>
                                @else
                                    <span class="text-slate-500">No</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-slate-500">Status</dt>
                            <dd class="mt-1">
                                @if($template->status === 'active')
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                        Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
                    <div>
                        <h2 class="text-base font-semibold text-red-900">Danger Zone</h2>
                        <p class="mt-1 text-sm text-red-700">
                            ลบ Template นี้ออกจากระบบ
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.content.templates.destroy', $template) }}"
                        onsubmit="return confirm('ยืนยันการลบ Template นี้?')"
                        class="mt-4"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700"
                        >
                            Delete Template
                        </button>
                    </form>
                </div>
            </div>

            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Description</h2>

                    <p class="mt-5 whitespace-pre-line text-sm text-slate-700">
                        {{ $template->description ?: '-' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Usage Note</h2>

                    <div class="mt-5 space-y-3 text-sm text-slate-600">
                        <p>
                            View Path ต้องชี้ไปยัง Blade view ที่มีอยู่จริง เช่น
                            <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">frontend.pages.default</code>
                        </p>
                        <p>
                            เมื่อ Page เลือก Template นี้ ฝั่ง frontend renderer จะใช้ค่า
                            <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">view_path</code>
                            เพื่อ render หน้า
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>