<x-layouts.admin title="แก้ไขบทความ" header="แก้ไขบทความ">
    <div class="space-y-6 text-white">

        {{-- Page Header --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        จัดการบทความ
                    </div>

                    <h1 class="text-2xl font-bold text-white">แก้ไขบทความ</h1>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        กำลังแก้ไข:
                        <span class="font-medium text-white">
                            {{ $article->content?->title }}
                        </span>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($article->content)
                        <a
                            href="{{ route('admin.content.template-preview', ['type' => 'article', 'content' => $article->content]) }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10 px-4 py-2.5 text-sm font-medium text-blue-300 transition hover:bg-blue-500/20"
                        >
                            ตัวอย่างเทมเพลต
                        </a>
                    @endif

                    <a
                        href="{{ route('admin.content.articles.show', $article) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        ดูรายละเอียด
                    </a>

                    <a
                        href="{{ route('admin.content.articles.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        กลับไปหน้ารายการ
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
            id="article-form"
            method="POST"
            action="{{ route('admin.content.articles.update', $article) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div x-data="{ activeTab: 'content' }" :class="`article-studio-tab-${activeTab}`" class="space-y-6">
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-slate-950/60 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="grid xl:grid-cols-[minmax(0,1fr)_340px]">
                        <div class="border-b border-white/10 p-5 xl:border-b-0 xl:border-r">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">Article Studio</p>
                            <h2 class="mt-1 text-lg font-semibold text-white">จัดการบทความ</h2>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-400">แก้เนื้อหา สื่อ SEO และการเผยแพร่ผ่าน tabs ด้านล่าง โดยยังใช้ข้อมูลชุดเดิมครบถ้วน</p>
                        </div>
                        <aside class="space-y-3 bg-white/[0.03] p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">สถานะงาน</p>
                            <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                                <p class="text-sm font-semibold text-white">{{ $article->content?->title ?? 'ยังไม่มีชื่อ' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $article->content?->status ?? 'draft' }}</p>
                            </div>
                        </aside>
                    </div>
                </section>

                <div class="sticky top-4 z-20 rounded-3xl border border-white/10 bg-slate-950/90 p-2 shadow-xl shadow-slate-950/30 backdrop-blur">
                    <div class="grid gap-2 md:grid-cols-5">
                        <button type="button" @click="activeTab = 'content'" :class="activeTab === 'content' ? 'border-blue-400/30 bg-blue-500/20 text-blue-100' : 'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white'" class="rounded-2xl border px-4 py-3 text-sm font-semibold transition">เนื้อหา</button>
                        <button type="button" @click="activeTab = 'taxonomy'" :class="activeTab === 'taxonomy' ? 'border-blue-400/30 bg-blue-500/20 text-blue-100' : 'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white'" class="rounded-2xl border px-4 py-3 text-sm font-semibold transition">หมวดหมู่</button>
                        <button type="button" @click="activeTab = 'media'" :class="activeTab === 'media' ? 'border-blue-400/30 bg-blue-500/20 text-blue-100' : 'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white'" class="rounded-2xl border px-4 py-3 text-sm font-semibold transition">สื่อ</button>
                        <button type="button" @click="activeTab = 'seo'" :class="activeTab === 'seo' ? 'border-blue-400/30 bg-blue-500/20 text-blue-100' : 'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white'" class="rounded-2xl border px-4 py-3 text-sm font-semibold transition">SEO</button>
                        <button type="button" @click="activeTab = 'publish'" :class="activeTab === 'publish' ? 'border-blue-400/30 bg-blue-500/20 text-blue-100' : 'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white'" class="rounded-2xl border px-4 py-3 text-sm font-semibold transition">เผยแพร่</button>
                    </div>
                </div>

                <div class="space-y-6">
                    @include('admin.content.articles._form', ['article' => $article])
                </div>
            </div>

            {{-- Sticky การจัดการ Bar --}}
            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">
                        ตรวจสอบข้อมูลก่อนบันทึกการแก้ไข
                    </p>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('admin.content.articles.index') }}"
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
</x-layouts.admin>
