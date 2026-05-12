<x-layouts.admin title="รายละเอียดบทความ" header="รายละเอียดบทความ">
    @php
        $content = $article->content;
        $coverUsage = $content?->mediaUsages?->firstWhere('role_key', 'cover');
        $coverMedia = $coverUsage?->media;
        $coverUrl = $coverMedia?->path ? asset('storage/' . $coverMedia->path) : null;

        $statusLabel = match ($content?->status) {
            'draft' => 'ฉบับร่าง',
            'published' => 'เผยแพร่แล้ว',
            'archived' => 'เก็บถาวร',
            default => 'ไม่ทราบสถานะ',
        };

        $statusClass = match ($content?->status) {
            'draft' => 'border-yellow-400/20 bg-yellow-500/10 text-yellow-300',
            'published' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300',
            'archived' => 'border-white/10 bg-white/5 text-slate-300',
            default => 'border-red-400/20 bg-red-500/10 text-red-300',
        };
    @endphp

    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                        Article Detail
                    </p>
                    <h1 class="text-2xl font-bold text-white">
                        {{ $content?->title ?? 'รายละเอียดบทความ' }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-400">
                        แสดงรายละเอียดบทความ เนื้อหา SEO หมวดหมู่ แท็ก รูปภาพ สถิติ และข้อมูลการเผยแพร่
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('admin.content.articles.edit', $article) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                    >
                        แก้ไข
                    </a>

                    <a
                        href="{{ route('admin.content.articles.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        กลับไปหน้ารายการ
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-5">

                {{-- Cover --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">รูปภาพหน้าปก</h2>
                        <p class="text-sm text-slate-400">รูปหลักที่ใช้แสดงผลในหน้าบทความ</p>
                    </div>

                    <div class="p-5">
                        @if ($coverMedia && $coverUrl)
                            <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40">
                                <div class="aspect-video bg-slate-950">
                                    <img
                                        src="{{ $coverUrl }}"
                                        alt="{{ $coverMedia->alt_text ?: $coverMedia->title ?: $content?->title ?: 'รูปภาพหน้าปก' }}"
                                        class="h-full w-full object-cover"
                                    >
                                </div>

                                <div class="grid gap-3 p-4 text-sm text-slate-300 md:grid-cols-2">
                                    <div>
                                        <span class="text-slate-500">ชื่อไฟล์:</span>
                                        {{ $coverMedia->original_filename ?? '-' }}
                                    </div>
                                    <div>
                                        <span class="text-slate-500">ประเภท:</span>
                                        {{ $coverMedia->media_type ?? '-' }}
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Alt:</span>
                                        {{ $coverMedia->alt_text ?: '-' }}
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Caption:</span>
                                        {{ $coverMedia->caption ?: '-' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed border-white/10 bg-slate-950/40 px-4 py-10 text-center">
                                <p class="text-base font-medium text-slate-300">ไม่มีรูปภาพหน้าปก</p>
                                <p class="mt-1 text-sm text-slate-500">ยังไม่ได้เลือก cover media สำหรับบทความนี้</p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Content Information --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">ข้อมูลบทความ</h2>
                        <p class="text-sm text-slate-400">ข้อมูลหลักจาก content และ article record</p>
                    </div>

                    <div class="grid gap-3 p-5 md:grid-cols-2">
                        @foreach ([
                            'ชื่อบทความ' => $content?->title ?? '-',
                            'ชื่อภาษาอังกฤษ' => $article->title_en ?: '-',
                            'Slug' => $content?->slug ?? '-',
                            'ผู้เขียน' => $article->author_name ?: '-',
                            'เวลาอ่าน' => $article->reading_time_minutes ? $article->reading_time_minutes . ' นาที' : '-',
                            'รูปแบบเนื้อหา' => $article->body_format ?: '-',
                            'เผยแพร่เมื่อ' => $content?->published_at?->format('d/m/Y H:i') ?? '-',
                            'เริ่มแสดง' => $article->scheduled_at?->format('d/m/Y H:i') ?? '-',
                            'หมดอายุ' => $article->expired_at?->format('d/m/Y H:i') ?? '-',
                            'สร้างเมื่อ' => $article->created_at?->format('d/m/Y H:i') ?? '-',
                            'แก้ไขล่าสุด' => $article->updated_at?->format('d/m/Y H:i') ?? '-',
                        ] as $label => $value)
                            <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</div>
                                <div class="mt-1 break-all text-sm text-slate-200">{{ $value }}</div>
                            </div>
                        @endforeach

                        <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">สถานะ</div>
                            <div class="mt-2">
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-medium {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Text Content --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">เนื้อหา</h2>
                    </div>

                    <div class="space-y-4 p-5">
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">คำโปรย</div>
                            <div class="mt-2 whitespace-pre-line rounded-2xl border border-white/10 bg-slate-950/40 p-4 text-sm text-slate-200">
                                {{ $content?->excerpt ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">คำโปรยภาษาอังกฤษ</div>
                            <div class="mt-2 whitespace-pre-line rounded-2xl border border-white/10 bg-slate-950/40 p-4 text-sm text-slate-200">
                                {{ $article->excerpt_en ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">รายละเอียดเพิ่มเติม</div>
                            <div class="mt-2 whitespace-pre-line rounded-2xl border border-white/10 bg-slate-950/40 p-4 text-sm text-slate-200">
                                {{ $content?->description ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">เนื้อหาบทความ</div>
                            <div class="mt-2 max-h-[520px] overflow-y-auto whitespace-pre-line rounded-2xl border border-white/10 bg-slate-950/60 p-4 font-mono text-sm text-slate-200">
                                {{ $article->body ?: '-' }}
                            </div>
                        </div>
                    </div>
                </section>

                {{-- SEO --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">SEO</h2>
                        <p class="text-sm text-slate-400">ข้อมูลสำหรับ Search Engine และ Social Preview</p>
                    </div>

                    <div class="space-y-4 p-5">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Meta Title</div>
                            <div class="mt-1 text-sm text-slate-200">{{ $content?->meta_title ?: '-' }}</div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Meta Description</div>
                            <div class="mt-1 whitespace-pre-line text-sm text-slate-200">{{ $content?->meta_description ?: '-' }}</div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">SEO Keywords</div>
                            <div class="mt-1 whitespace-pre-line text-sm text-slate-200">{{ $article->seo_keywords ?: '-' }}</div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="space-y-5 xl:sticky xl:top-6 xl:self-start">

                {{-- Display Settings --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">การแสดงผล</h2>
                    </div>

                    <div class="space-y-3 p-5 text-sm">
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                            <span class="text-slate-400">เปิดความคิดเห็น</span>
                            <span class="font-medium {{ $article->allow_comments ? 'text-emerald-300' : 'text-red-300' }}">
                                {{ $article->allow_comments ? 'เปิด' : 'ปิด' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                            <span class="text-slate-400">แสดงบนหน้าแรก</span>
                            <span class="font-medium {{ $article->show_on_homepage ? 'text-emerald-300' : 'text-slate-400' }}">
                                {{ $article->show_on_homepage ? 'แสดง' : 'ไม่แสดง' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                            <span class="text-slate-400">บทความแนะนำ</span>
                            <span class="font-medium {{ $content?->is_featured ? 'text-yellow-300' : 'text-slate-400' }}">
                                {{ $content?->is_featured ? 'ใช่' : 'ไม่ใช่' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                            <span class="text-slate-400">ยอดนิยม</span>
                            <span class="font-medium {{ $content?->is_popular ? 'text-blue-300' : 'text-slate-400' }}">
                                {{ $content?->is_popular ? 'ใช่' : 'ไม่ใช่' }}
                            </span>
                        </div>
                    </div>
                </section>

                {{-- Statistics --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">สถิติ</h2>
                    </div>

                    <div class="grid gap-3 p-5">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="text-xs text-slate-500">เข้าชม</div>
                            <div class="mt-1 text-2xl font-semibold text-white">{{ $article->stat?->view_count ?? 0 }}</div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="text-xs text-slate-500">ถูกใจ</div>
                            <div class="mt-1 text-2xl font-semibold text-white">{{ $article->stat?->like_count ?? 0 }}</div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="text-xs text-slate-500">บันทึก</div>
                            <div class="mt-1 text-2xl font-semibold text-white">{{ $article->stat?->bookmark_count ?? 0 }}</div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="text-xs text-slate-500">แชร์</div>
                            <div class="mt-1 text-2xl font-semibold text-white">{{ $article->stat?->share_count ?? 0 }}</div>
                        </div>
                    </div>
                </section>

                {{-- Categories --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">หมวดหมู่</h2>
                    </div>

                    <div class="flex flex-wrap gap-2 p-5">
                        @forelse ($content?->categories ?? [] as $category)
                            <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                {{ $category->name }}
                            </span>
                        @empty
                            <span class="text-sm text-slate-500">ยังไม่ได้เลือกหมวดหมู่</span>
                        @endforelse
                    </div>
                </section>

                {{-- Tags --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">แท็ก</h2>
                    </div>

                    <div class="flex flex-wrap gap-2 p-5">
                        @forelse ($article->tags as $tag)
                            <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                {{ $tag->name }}
                            </span>
                        @empty
                            <span class="text-sm text-slate-500">ยังไม่ได้เลือกแท็ก</span>
                        @endforelse
                    </div>
                </section>

                {{-- Related Articles --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">บทความที่เกี่ยวข้อง</h2>
                    </div>

                    <div class="space-y-3 p-5">
                        @forelse ($article->relatedArticles as $relatedArticle)
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm text-slate-300">
                                <div class="font-medium text-slate-200">
                                    {{ $relatedArticle->content?->title ?? 'ไม่มีชื่อบทความ' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    #{{ $relatedArticle->id }} | {{ $relatedArticle->content?->slug ?? '-' }}
                                </div>
                            </div>
                        @empty
                            <span class="text-sm text-slate-500">ยังไม่มีบทความที่เกี่ยวข้อง</span>
                        @endforelse
                    </div>
                </section>

                {{-- Cover Media Metadata --}}
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-3">
                        <h2 class="text-base font-semibold text-white">ข้อมูลไฟล์หน้าปก</h2>
                    </div>

                    <div class="p-5">
                        @if ($coverMedia)
                            <div class="space-y-2 text-sm text-slate-300">
                                <div><span class="text-slate-500">ID:</span> #{{ $coverMedia->id }}</div>
                                <div><span class="text-slate-500">Title:</span> {{ $coverMedia->title ?: '-' }}</div>
                                <div><span class="text-slate-500">File:</span> {{ $coverMedia->original_filename ?: '-' }}</div>
                                <div><span class="text-slate-500">MIME:</span> {{ $coverMedia->mime_type ?: '-' }}</div>
                                <div><span class="text-slate-500">Size:</span> {{ $coverMedia->file_size ? number_format($coverMedia->file_size / 1024, 2) . ' KB' : '-' }}</div>
                                <div><span class="text-slate-500">Path:</span> <span class="break-all">{{ $coverMedia->path ?: '-' }}</span></div>
                            </div>
                        @else
                            <span class="text-sm text-slate-500">ไม่มีข้อมูลไฟล์หน้าปก</span>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    </div>

</x-layouts.admin>
