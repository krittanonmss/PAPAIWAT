<x-layouts.admin title="สร้างโฟลเดอร์สื่อ" header="สร้างโฟลเดอร์สื่อ">
    <div class="space-y-6 text-white">

        {{-- Page Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        คลังสื่อ
                    </div>

                    <h1 class="text-2xl font-bold text-white">สร้างโฟลเดอร์สื่อ</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        เพิ่มโฟลเดอร์ใหม่สำหรับจัดเก็บไฟล์ รูปภาพ เอกสาร และสื่อในระบบ
                    </p>
                </div>

                <a
                    href="{{ route('admin.media-folders.index') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                >
                    กลับไปหน้ารายการ
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

        <form
            action="{{ route('admin.media-folders.store') }}"
            method="POST"
            class="space-y-6"
        >
            @csrf

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">

                {{-- Main Form --}}
                <div class="space-y-6">
                    @include('admin.content.media.folders._form')
                </div>

                {{-- Side Panel --}}
                <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
                    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h3 class="text-sm font-semibold text-white">เช็กลิสต์ก่อนบันทึก</h3>

                        <div class="mt-4 space-y-3">
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm font-medium text-slate-200">ชื่อโฟลเดอร์</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    ตั้งชื่อให้เข้าใจง่าย และสื่อถึงประเภทไฟล์ที่จัดเก็บ
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm font-medium text-slate-200">โครงสร้างโฟลเดอร์</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    เลือกโฟลเดอร์แม่ให้ถูกต้อง หากต้องการจัดเก็บแบบลำดับชั้น
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm font-medium text-slate-200">สถานะการใช้งาน</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    เปิดใช้งานเมื่อโฟลเดอร์พร้อมใช้ใน คลังสื่อ
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Sticky การจัดการ Bar --}}
            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">
                        ตรวจสอบข้อมูลโฟลเดอร์ก่อนกดบันทึก
                    </p>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('admin.media-folders.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                        >
                            ยกเลิก
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
                        >
                            บันทึกโฟลเดอร์
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>