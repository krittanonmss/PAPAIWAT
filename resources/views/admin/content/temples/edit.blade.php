<x-layouts.admin title="{{ $title ?? 'Edit Temple' }}" header="แก้ไขข้อมูลวัด">
    <div class="space-y-6 text-white">

        {{-- Page Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                        <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                            Temple Management
                        </div>

                    <h1 class="text-2xl font-bold text-white">แก้ไขข้อมูลวัด</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        กำลังแก้ไข:
                        <span class="font-medium text-white">
                            {{ $temple->content?->title }}
                        </span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @if ($temple->content)
                        <a
                            href="{{ route('admin.content.template-preview', ['type' => 'temple', 'content' => $temple->content]) }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10 px-4 py-2.5 text-sm font-medium text-blue-300 transition hover:bg-blue-500/20"
                        >
                            Preview template
                        </a>
                    @endif

                    <a
                        href="{{ route('admin.temples.show', $temple) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        ดูรายละเอียด
                    </a>

                    <a
                        href="{{ route('admin.temples.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        กลับไปรายการวัด
                    </a>
                </div>
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
            id="temple-form"
            action="{{ route('admin.temples.update', $temple) }}"
            method="POST"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">

                {{-- Main Form --}}
                <div class="space-y-6">
                    @include('admin.content.temples._form')
                </div>

                <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h3 class="text-sm font-semibold text-white">ไปยังส่วนที่ต้องแก้</h3>

                        <div class="mt-4 space-y-2 text-sm">
                            <a href="#basic-info" class="block rounded-xl border border-blue-400/20 bg-blue-500/10 px-4 py-3 text-blue-200 hover:bg-blue-500/20">
                                ข้อมูลหลักและเนื้อหา
                            </a>
                            <a href="#temple-details" class="block rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-slate-300 hover:bg-white/[0.06]">
                                ข้อมูลเฉพาะของวัด
                            </a>
                            <a href="#media-section" class="block rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-slate-300 hover:bg-white/[0.06]">
                                รูปภาพ หมวดหมู่ และที่ตั้ง
                            </a>
                            <a href="#temple-publishing" class="block rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-slate-300 hover:bg-white/[0.06]">
                                การเผยแพร่
                            </a>
                        </div>

                        <div class="mt-5 rounded-xl border border-amber-400/20 bg-amber-500/10 px-4 py-3">
                            <p class="text-xs leading-5 text-amber-100">
                                ข้อมูลที่แก้ใน editor จะถูกบันทึกเป็น HTML ที่ผ่านการกรอง tag ตอนบันทึก
                            </p>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Sticky Action Bar --}}
            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <button
                        type="button"
                        onclick="if(confirm('ยืนยันการลบข้อมูลวัดนี้? ไม่สามารถกู้คืนได้')) document.getElementById('delete-temple-form').submit()"
                        class="inline-flex items-center justify-center rounded-xl border border-rose-400/30 bg-rose-500/10 px-5 py-2.5 text-sm font-medium text-rose-300 hover:bg-rose-500/20"
                    >
                        ลบข้อมูลวัด
                    </button>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('admin.temples.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                        >
                            ยกเลิก
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
                        >
                            บันทึกการแก้ไข
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Delete form --}}
    <form
        id="delete-temple-form"
        method="POST"
        action="{{ route('admin.temples.destroy', $temple) }}"
        class="hidden"
    >
        @csrf
        @method('DELETE')
    </form>
</x-layouts.admin>
