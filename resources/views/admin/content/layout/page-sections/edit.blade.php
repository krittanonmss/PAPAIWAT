<x-layouts.admin :title="'แก้ไขเซกชันของหน้า'">
    <div class="space-y-6 text-white">

        {{-- Page Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        เซกชันของหน้า
                    </div>

                    <h1 class="text-2xl font-bold text-white">แก้ไขเซกชัน</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        กำลังแก้ไขเซกชัน:
                        <span class="font-medium text-white">{{ $section->name }}</span>
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.pages.show', $page) }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                >
                    กลับไปหน้ารายละเอียด
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="space-y-3">
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-emerald-950/20">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200 shadow-lg shadow-rose-950/20">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-4 text-sm text-rose-200 shadow-lg shadow-rose-950/20">
                    <p class="font-semibold text-rose-100">กรุณาตรวจสอบข้อมูลที่กรอก</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <form
                id="update-section-form"
                method="POST"
                action="{{ route('admin.content.pages.sections.update', [$page, $section]) }}"
            >
                @csrf
                @method('PUT')

                @include('admin.content.layout.page-sections._form', ['section' => $section])
            </form>

            {{-- Sticky การจัดการ Bar --}}
            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <form
                        method="POST"
                        action="{{ route('admin.content.pages.sections.destroy', [$page, $section]) }}"
                        onsubmit="return confirm('ยืนยันการลบเซกชันนี้?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl border border-rose-400/30 bg-rose-500/10 px-5 py-2.5 text-sm font-medium text-rose-200 transition hover:bg-rose-500/20 hover:text-rose-100"
                        >
                            ลบเซกชัน
                        </button>
                    </form>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('admin.content.pages.show', $page) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                        >
                            ยกเลิก
                        </a>

                        <button
                            type="submit"
                            form="update-section-form"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
                        >
                            บันทึกการแก้ไข
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
