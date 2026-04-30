<x-layouts.admin :title="$template->name">
    <div class="space-y-6 text-white">

        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        Template Detail
                    </div>

                    <h1 class="text-2xl font-bold text-white">{{ $template->name }}</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        รายละเอียด Template และการใช้งานกับหน้าเว็บไซต์
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('admin.content.templates.edit', $template) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-medium text-white shadow-lg shadow-amber-950/40 transition hover:opacity-90"
                    >
                        แก้ไข
                    </a>

                    <a
                        href="{{ route('admin.content.templates.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        กลับไปรายการ Template
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
            $statusClass = $template->status === 'active'
                ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
                : 'border-slate-400/20 bg-slate-500/10 text-slate-300';
        @endphp

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Main Content --}}
            <div class="space-y-6 xl:col-span-2">
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Description</h2>
                    </div>

                    <div class="p-6">
                        <p class="whitespace-pre-line text-sm leading-6 text-slate-300">
                            {{ $template->description ?: '-' }}
                        </p>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Usage Note</h2>
                    </div>

                    <div class="space-y-4 p-6 text-sm leading-6 text-slate-300">
                        <p>
                            View Path ต้องชี้ไปยัง Blade view ที่มีอยู่จริง เช่น
                            <code class="rounded-lg border border-white/10 bg-slate-950/40 px-2 py-1 text-xs text-blue-300">frontend.pages.default</code>
                        </p>

                        <p>
                            เมื่อ Page เลือก Template นี้ ฝั่ง frontend renderer จะใช้ค่า
                            <code class="rounded-lg border border-white/10 bg-slate-950/40 px-2 py-1 text-xs text-blue-300">view_path</code>
                            เพื่อ render หน้า
                        </p>
                    </div>
                </section>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-base font-semibold text-white">Template Detail</h2>
                    </div>

                    <div class="divide-y divide-white/10">
                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">Name</p>
                            <p class="mt-0.5 break-words text-sm font-medium text-slate-200">{{ $template->name }}</p>
                        </div>

                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">Key</p>
                            <div class="mt-1">
                                <span class="inline-flex rounded-xl border border-white/10 bg-slate-950/40 px-2.5 py-1 text-xs font-medium text-slate-300">
                                    {{ $template->key }}
                                </span>
                            </div>
                        </div>

                        <div class="px-6 py-3">
                            <p class="text-xs text-slate-500">View Path</p>
                            <div class="mt-1">
                                <code class="inline-flex max-w-full break-all rounded-xl border border-white/10 bg-slate-950/40 px-2.5 py-1 text-xs text-blue-300">
                                    {{ $template->view_path }}
                                </code>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Pages</span>
                            <span class="text-sm font-medium text-slate-200">{{ $template->pages_count }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Sort Order</span>
                            <span class="text-sm font-medium text-slate-200">{{ $template->sort_order }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Default</span>
                            @if($template->is_default)
                                <span class="inline-flex rounded-full border border-indigo-400/20 bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-300">
                                    Default
                                </span>
                            @else
                                <span class="text-sm text-slate-500">No</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-3">
                            <span class="text-sm text-slate-400">Status</span>
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($template->status) }}
                            </span>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-rose-400/20 bg-rose-500/10 shadow-xl shadow-rose-950/20 backdrop-blur">
                    <div class="border-b border-rose-400/20 px-6 py-4">
                        <h2 class="text-base font-semibold text-rose-200">Danger Zone</h2>
                    </div>

                    <div class="p-6">
                        <p class="mb-4 text-sm text-rose-200/80">
                            ลบ Template นี้ออกจากระบบ
                        </p>

                        <form
                            method="POST"
                            action="{{ route('admin.content.templates.destroy', $template) }}"
                            onsubmit="return confirm('ยืนยันการลบ Template นี้?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-2.5 text-sm font-medium text-rose-200 transition hover:bg-rose-500/20"
                            >
                                Delete Template
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.admin>