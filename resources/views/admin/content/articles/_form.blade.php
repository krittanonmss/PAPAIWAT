@php
    /** @var \App\Models\Content\Article\Article|null $article */
    $content = $article->content ?? null;

    $selectedCategoryIds = old(
        'category_ids',
        isset($article) ? $content?->categories->pluck('id')->all() ?? [] : []
    );

    $selectedTagIds = old(
        'tag_ids',
        isset($article) ? $article->tags->pluck('id')->all() ?? [] : []
    );

    $selectedRelatedArticleIds = old(
        'related_article_ids',
        isset($article) ? $article->relatedArticles->pluck('id')->all() ?? [] : []
    );

    $selectedCoverMediaId = old(
        'cover_media_id',
        isset($article)
            ? optional($content?->mediaUsages?->firstWhere('role_key', 'cover'))->media_id
            : null
    );

    $detailTemplates = $detailTemplates ?? collect();
    $selectedTemplateId = old('template_id', $content?->template_id);
    $templatePreviewUrl = $templatePreviewUrl ?? null;
    $templatePreviewSrc = $templatePreviewUrl
        ? $templatePreviewUrl . '?' . http_build_query(array_filter([
            'template_id' => $selectedTemplateId,
            '_preview_ts' => time(),
        ]))
        : null;
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        {{-- Main Content --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">ข้อมูลหลักของบทความ</h2>
                <p class="mt-1 text-xs text-slate-400">กรอกชื่อบทความ slug คำโปรย เนื้อหา และข้อมูลผู้เขียน</p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ชื่อบทความ <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $content->title ?? '') }}"
                        class="@error('title') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="กรอกชื่อบทความ"
                    >
                    @error('title')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-300">
                        Slug
                    </label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $content->slug ?? '') }}"
                        class="@error('slug') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="เว้นว่างไว้เพื่อให้ระบบสร้างให้อัตโนมัติ"
                    >
                    @error('slug')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="template_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                        Template หน้า Detail
                    </label>
                    <select
                        id="template_id"
                        name="template_id"
                        data-template-preview-select
                        data-preview-target="article-template-preview"
                        data-preview-base="{{ $templatePreviewUrl }}"
                        class="@error('template_id') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">ใช้ค่าเริ่มต้นของบทความ</option>
                        @foreach ($detailTemplates as $template)
                            <option value="{{ $template->id }}" @selected((string) old('template_id', $content?->template_id) === (string) $template->id)>
                                {{ $template->name }} ({{ $template->key }})
                            </option>
                        @endforeach
                    </select>
                    @error('template_id')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500">
                        ถ้าไม่เลือก ระบบจะใช้ article-detail template ที่ active อยู่
                    </p>

                    @if ($templatePreviewSrc)
                        <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-slate-950/70">
                            <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-slate-200">Preview template</p>
                                    <p class="mt-0.5 text-xs text-slate-500">ใช้ข้อมูล article จาก database ล่าสุด กดบันทึกก่อนรีเฟรช preview</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        data-template-preview-refresh
                                        class="rounded-lg border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs font-medium text-slate-300 hover:bg-white/10"
                                    >
                                        รีเฟรชข้อมูลจาก DB
                                    </button>
                                    <a
                                        href="{{ $templatePreviewSrc }}"
                                        target="_blank"
                                        rel="noopener"
                                        data-template-preview-open
                                        class="rounded-lg border border-blue-400/20 bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-300 hover:bg-blue-500/20"
                                    >
                                        เปิดเต็มหน้า
                                    </a>
                                </div>
                            </div>
                            <iframe
                                id="article-template-preview"
                                src="{{ $templatePreviewSrc }}"
                                class="h-[520px] w-full bg-slate-950"
                                loading="lazy"
                            ></iframe>
                        </div>

                        <script>
                            document.querySelectorAll('[data-template-preview-select]').forEach((select) => {
                                if (select.dataset.previewBound === '1') {
                                    return;
                                }

                                select.dataset.previewBound = '1';
                                const updatePreview = () => {
                                    const baseUrl = select.dataset.previewBase;
                                    const frame = document.getElementById(select.dataset.previewTarget);
                                    const openLink = select.closest('div').querySelector('[data-template-preview-open]');

                                    if (!baseUrl || !frame) {
                                        return;
                                    }

                                    const url = new URL(baseUrl, window.location.origin);

                                    if (select.value) {
                                        url.searchParams.set('template_id', select.value);
                                    }

                                    url.searchParams.set('_preview_ts', Date.now().toString());

                                    frame.src = url.toString();

                                    if (openLink) {
                                        openLink.href = url.toString();
                                    }
                                };

                                select.addEventListener('change', updatePreview);

                                select.closest('div')
                                    .querySelector('[data-template-preview-refresh]')
                                    ?.addEventListener('click', updatePreview);
                            });
                        </script>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label for="title_en" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ชื่อบทความภาษาอังกฤษ
                    </label>
                    <input
                        type="text"
                        id="title_en"
                        name="title_en"
                        value="{{ old('title_en', $article->title_en ?? '') }}"
                        class="@error('title_en') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="กรอกชื่อบทความภาษาอังกฤษ"
                    >
                    @error('title_en')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-300">
                        คำโปรย
                    </label>
                    <textarea
                        id="excerpt"
                        name="excerpt"
                        rows="3"
                        class="@error('excerpt') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="สรุปบทความแบบสั้น"
                    >{{ old('excerpt', $content->excerpt ?? '') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="excerpt_en" class="mb-1.5 block text-sm font-medium text-slate-300">
                        คำโปรยภาษาอังกฤษ
                    </label>
                    <textarea
                        id="excerpt_en"
                        name="excerpt_en"
                        rows="3"
                        class="@error('excerpt_en') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="สรุปบทความภาษาอังกฤษแบบสั้น"
                    >{{ old('excerpt_en', $article->excerpt_en ?? '') }}</textarea>
                    @error('excerpt_en')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">
                        รายละเอียดเพิ่มเติม
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="@error('description') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="รายละเอียดเพิ่มเติมของบทความ"
                    >{{ old('description', $content->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="body_format" class="mb-1.5 block text-sm font-medium text-slate-300">
                        รูปแบบเนื้อหา <span class="text-rose-400">*</span>
                    </label>
                    <select
                        id="body_format"
                        name="body_format"
                        class="@error('body_format') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="markdown" @selected(old('body_format', $article->body_format ?? 'markdown') === 'markdown')>Markdown</option>
                        <option value="html" @selected(old('body_format', $article->body_format ?? 'markdown') === 'html')>HTML</option>
                        <option value="editorjs" @selected(old('body_format', $article->body_format ?? 'markdown') === 'editorjs')>EditorJS</option>
                    </select>
                    @error('body_format')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author_name" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ผู้เขียน
                    </label>
                    <input
                        type="text"
                        id="author_name"
                        name="author_name"
                        value="{{ old('author_name', $article->author_name ?? '') }}"
                        class="@error('author_name') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="กรอกชื่อผู้เขียน"
                    >
                    @error('author_name')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reading_time_minutes" class="mb-1.5 block text-sm font-medium text-slate-300">
                        เวลาอ่านโดยประมาณ (นาที)
                    </label>
                    <input
                        type="number"
                        id="reading_time_minutes"
                        name="reading_time_minutes"
                        min="1"
                        value="{{ old('reading_time_minutes', $article->reading_time_minutes ?? '') }}"
                        class="@error('reading_time_minutes') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                    @error('reading_time_minutes')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="published_at" class="mb-1.5 block text-sm font-medium text-slate-300">
                        วันที่เผยแพร่
                    </label>
                    <input
                        type="datetime-local"
                        id="published_at"
                        name="published_at"
                        value="{{ old('published_at', isset($content?->published_at) ? $content->published_at->format('Y-m-d\TH:i') : '') }}"
                        class="@error('published_at') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                    @error('published_at')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="scheduled_at" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ตั้งเวลาเริ่มแสดง
                    </label>
                    <input
                        type="datetime-local"
                        id="scheduled_at"
                        name="scheduled_at"
                        value="{{ old('scheduled_at', isset($article?->scheduled_at) ? $article->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                        class="@error('scheduled_at') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                    @error('scheduled_at')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expired_at" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ตั้งเวลาหมดอายุ
                    </label>
                    <input
                        type="datetime-local"
                        id="expired_at"
                        name="expired_at"
                        value="{{ old('expired_at', isset($article?->expired_at) ? $article->expired_at->format('Y-m-d\TH:i') : '') }}"
                        class="@error('expired_at') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                    @error('expired_at')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="body" class="mb-1.5 block text-sm font-medium text-slate-300">
                        เนื้อหาบทความ
                    </label>
                    <textarea
                        id="body"
                        name="body"
                        rows="14"
                        class="@error('body') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 font-mono text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="เขียนเนื้อหาบทความ"
                    >{{ old('body', $article->body ?? '') }}</textarea>
                    @error('body')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- SEO --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">SEO</h2>
                <p class="mt-1 text-xs text-slate-400">ข้อมูลสำหรับ Search Engine และการแชร์บทความ</p>
            </div>

            <div class="grid gap-6 p-6">
                <div>
                    <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-300">
                        Meta Title
                    </label>
                    <input
                        type="text"
                        id="meta_title"
                        name="meta_title"
                        value="{{ old('meta_title', $content->meta_title ?? '') }}"
                        class="@error('meta_title') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="หัวข้อ SEO"
                    >
                    @error('meta_title')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-300">
                        Meta Description
                    </label>
                    <textarea
                        id="meta_description"
                        name="meta_description"
                        rows="3"
                        class="@error('meta_description') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="คำอธิบาย SEO"
                    >{{ old('meta_description', $content->meta_description ?? '') }}</textarea>
                    @error('meta_description')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="seo_keywords" class="mb-1.5 block text-sm font-medium text-slate-300">
                        SEO Keywords
                    </label>
                    <textarea
                        id="seo_keywords"
                        name="seo_keywords"
                        rows="3"
                        class="@error('seo_keywords') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="คีย์เวิร์ด คั่นด้วย comma"
                    >{{ old('seo_keywords', $article->seo_keywords ?? '') }}</textarea>
                    @error('seo_keywords')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-6">
        {{-- Publishing --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">การเผยแพร่</h2>
                <p class="mt-1 text-xs text-slate-400">กำหนดสถานะและการแสดงผลของบทความ</p>
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                        สถานะ <span class="text-rose-400">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="@error('status') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="draft" @selected(old('status', $content->status ?? 'draft') === 'draft')>ฉบับร่าง</option>
                        <option value="published" @selected(old('status', $content->status ?? 'draft') === 'published')>เผยแพร่แล้ว</option>
                        <option value="archived" @selected(old('status', $content->status ?? 'draft') === 'archived')>เก็บถาวร</option>
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input type="hidden" name="is_featured" value="0">
                        <input
                            type="checkbox"
                            name="is_featured"
                            value="1"
                            @checked(old('is_featured', $content->is_featured ?? false))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">บทความแนะนำ</div>
                            <div class="text-xs text-slate-500">ใช้เน้นบทความในส่วนสำคัญของเว็บไซต์</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input type="hidden" name="is_popular" value="0">
                        <input
                            type="checkbox"
                            name="is_popular"
                            value="1"
                            @checked(old('is_popular', $content->is_popular ?? false))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">บทความยอดนิยม</div>
                            <div class="text-xs text-slate-500">กำหนดให้บทความนี้อยู่ในกลุ่มยอดนิยม</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input type="hidden" name="allow_comments" value="0">
                        <input
                            type="checkbox"
                            name="allow_comments"
                            value="1"
                            @checked(old('allow_comments', $article->allow_comments ?? true))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">เปิดความคิดเห็น</div>
                            <div class="text-xs text-slate-500">อนุญาตให้ผู้ใช้แสดงความคิดเห็นในบทความนี้</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input type="hidden" name="show_on_homepage" value="0">
                        <input
                            type="checkbox"
                            name="show_on_homepage"
                            value="1"
                            @checked(old('show_on_homepage', $article->show_on_homepage ?? false))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">แสดงบนหน้าแรก</div>
                            <div class="text-xs text-slate-500">นำบทความนี้ไปแสดงใน section ของหน้าแรก</div>
                        </div>
                    </label>
                </div>
            </div>
        </section>

        {{-- Categories --}}
        <section
            x-data="{
                search: '',
                selectedCategoryIds: @js(array_map('strval', $selectedCategoryIds)),
                categories: [
                    @foreach ($categories as $category)
                        {
                            id: '{{ $category->id }}',
                            name: @js($category->name),
                            typeKey: @js($category->type_key),
                            sortOrder: '{{ $category->sort_order }}',
                        },
                    @endforeach
                ],
                isSelected(id) {
                    return this.selectedCategoryIds.includes(String(id));
                },
                toggle(id) {
                    id = String(id);

                    if (this.isSelected(id)) {
                        this.selectedCategoryIds = this.selectedCategoryIds.filter((item) => item !== id);
                        return;
                    }

                    this.selectedCategoryIds.push(id);
                },
                get filteredCategories() {
                    const keyword = this.search.toLowerCase().trim();

                    if (!keyword) {
                        return this.categories;
                    }

                    return this.categories.filter((category) => {
                        return category.name.toLowerCase().includes(keyword)
                            || category.typeKey.toLowerCase().includes(keyword)
                            || String(category.id).includes(keyword);
                    });
                },
            }"
            class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        >
            <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">หมวดหมู่</h2>
                    <p class="mt-1 text-xs text-slate-400">
                        เลือกหมวดหมู่ที่เกี่ยวข้องกับบทความ
                    </p>
                </div>

                <a
                    href="{{ route('admin.categories.index') }}"
                    target="_blank"
                    class="inline-flex items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                >
                    + ไปจัดการหมวดหมู่
                </a>
            </div>

            <div class="space-y-4 p-6">
                <div>
                    <label for="category_search" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ค้นหาหมวดหมู่
                    </label>
                    <input
                        type="text"
                        id="category_search"
                        x-model="search"
                        placeholder="ค้นหาจากชื่อหมวดหมู่ ประเภท หรือ ID"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="flex items-center justify-between rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3">
                    <p class="text-xs text-slate-400">
                        เลือกแล้ว
                        <span class="font-semibold text-blue-300" x-text="selectedCategoryIds.length"></span>
                        หมวดหมู่
                    </p>

                    <button
                        type="button"
                        x-show="selectedCategoryIds.length > 0"
                        @click="selectedCategoryIds = []"
                        class="text-xs font-medium text-rose-300 transition hover:text-rose-200"
                    >
                        ล้างทั้งหมด
                    </button>
                </div>

                <div class="max-h-72 space-y-3 overflow-y-auto pr-1">
                    @foreach ($categories as $category)
                        <label
                            x-show="filteredCategories.some((item) => String(item.id) === '{{ $category->id }}')"
                            class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                            :class="isSelected('{{ $category->id }}')
                                ? 'border-blue-400/50 bg-blue-500/10 ring-1 ring-blue-500/20'
                                : 'border-white/10 bg-slate-950/40 hover:border-blue-400/30 hover:bg-white/5'"
                        >
                            <input
                                type="checkbox"
                                name="category_ids[]"
                                value="{{ $category->id }}"
                                x-model="selectedCategoryIds"
                                class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                            >

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-medium text-slate-200">
                                            {{ $category->name }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            Type: {{ $category->type_key }} | Sort: {{ $category->sort_order }}
                                        </div>
                                    </div>

                                    <span
                                        x-show="isSelected('{{ $category->id }}')"
                                        class="shrink-0 rounded-full border border-blue-400/20 bg-blue-500/10 px-2.5 py-1 text-[11px] font-medium text-blue-300"
                                    >
                                        เลือกแล้ว
                                    </span>
                                </div>
                            </div>
                        </label>
                    @endforeach

                    <div
                        x-show="filteredCategories.length === 0"
                        class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-6 text-center text-sm text-slate-500"
                    >
                        ไม่พบหมวดหมู่ที่ตรงกับคำค้นหา
                    </div>

                    @if ($categories->isEmpty())
                        <p class="text-sm text-slate-500">ไม่พบหมวดหมู่บทความที่เปิดใช้งาน</p>
                    @endif
                </div>

                @error('category_ids')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
                @error('category_ids.*')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- Tags --}}
        <section
            x-data="{
                search: '',
                selectedTagIds: @js(array_map('strval', $selectedTagIds)),
                tags: [
                    @foreach ($tags as $tag)
                        {
                            id: '{{ $tag->id }}',
                            name: @js($tag->name),
                            slug: @js($tag->slug),
                        },
                    @endforeach
                ],
                isSelected(id) {
                    return this.selectedTagIds.includes(String(id));
                },
                get filteredTags() {
                    const keyword = this.search.toLowerCase().trim();

                    if (!keyword) {
                        return this.tags;
                    }

                    return this.tags.filter((tag) => {
                        return tag.name.toLowerCase().includes(keyword)
                            || tag.slug.toLowerCase().includes(keyword)
                            || String(tag.id).includes(keyword);
                    });
                },
            }"
            class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        >
            <div class="flex flex-col gap-3 border-b border-white/10 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">แท็ก</h2>
                    <p class="mt-1 text-xs text-slate-400">
                        เลือกแท็กที่เกี่ยวข้องกับบทความ
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.article-tags.index') }}"
                    target="_blank"
                    class="inline-flex items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                >
                    + ไปจัดการแท็ก
                </a>
            </div>

            <div class="space-y-4 p-6">
                <div>
                    <label for="tag_search" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ค้นหาแท็ก
                    </label>
                    <input
                        type="text"
                        id="tag_search"
                        x-model="search"
                        placeholder="ค้นหาจากชื่อแท็ก slug หรือ ID"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="flex items-center justify-between rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3">
                    <p class="text-xs text-slate-400">
                        เลือกแล้ว
                        <span class="font-semibold text-blue-300" x-text="selectedTagIds.length"></span>
                        แท็ก
                    </p>

                    <button
                        type="button"
                        x-show="selectedTagIds.length > 0"
                        @click="selectedTagIds = []"
                        class="text-xs font-medium text-rose-300 transition hover:text-rose-200"
                    >
                        ล้างทั้งหมด
                    </button>
                </div>

                <div class="max-h-72 space-y-3 overflow-y-auto pr-1">
                    @foreach ($tags as $tag)
                        <label
                            x-show="filteredTags.some((item) => String(item.id) === '{{ $tag->id }}')"
                            class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                            :class="isSelected('{{ $tag->id }}')
                                ? 'border-blue-400/50 bg-blue-500/10 ring-1 ring-blue-500/20'
                                : 'border-white/10 bg-slate-950/40 hover:border-blue-400/30 hover:bg-white/5'"
                        >
                            <input
                                type="checkbox"
                                name="tag_ids[]"
                                value="{{ $tag->id }}"
                                x-model="selectedTagIds"
                                class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                            >

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-medium text-slate-200">
                                            {{ $tag->name }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            Slug: {{ $tag->slug }}
                                        </div>
                                    </div>

                                    <span
                                        x-show="isSelected('{{ $tag->id }}')"
                                        class="shrink-0 rounded-full border border-blue-400/20 bg-blue-500/10 px-2.5 py-1 text-[11px] font-medium text-blue-300"
                                    >
                                        เลือกแล้ว
                                    </span>
                                </div>
                            </div>
                        </label>
                    @endforeach

                    <div
                        x-show="filteredTags.length === 0"
                        class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-6 text-center text-sm text-slate-500"
                    >
                        ไม่พบแท็กที่ตรงกับคำค้นหา
                    </div>

                    @if ($tags->isEmpty())
                        <p class="text-sm text-slate-500">ไม่พบแท็กบทความที่เปิดใช้งาน</p>
                    @endif
                </div>

                @error('tag_ids')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
                @error('tag_ids.*')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- Cover Media --}}
        <section
            x-data="{
                selectedMediaId: '{{ $selectedCoverMediaId }}',
                search: '',
                mediaItems: [
                    @foreach ($mediaItems as $mediaItem)
                        {
                            id: '{{ $mediaItem->id }}',
                            title: @js($mediaItem->title ?: $mediaItem->original_filename),
                            filename: @js($mediaItem->original_filename),
                            url: @js(
                                $mediaItem->path
                                    ? asset('storage/' . $mediaItem->path)
                                    : null
                            ),
                            mediaType: @js($mediaItem->media_type),
                        },
                    @endforeach
                ],
                get selectedMedia() {
                    return this.mediaItems.find((item) => String(item.id) === String(this.selectedMediaId)) || null;
                },
                get filteredMediaItems() {
                    const keyword = this.search.toLowerCase().trim();

                    return this.mediaItems.filter((item) => {
                        if (item.mediaType !== 'image') {
                            return false;
                        }

                        if (!keyword) {
                            return true;
                        }

                        return item.title.toLowerCase().includes(keyword)
                            || item.filename.toLowerCase().includes(keyword)
                            || String(item.id).includes(keyword);
                    });
                },
            }"
            class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        >
           <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-white">รูปภาพหน้าปก</h2>
                    <p class="mt-1 text-xs text-slate-400">
                        เลือกรูปภาพจาก Media Library เพื่อใช้เป็นภาพหน้าปกของบทความ
                    </p>
                </div>

                <a
                    href="{{ route('admin.media.index') }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded-xl border border-blue-400/20 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                >
                    + ไปจัดการมีเดีย
                </a>
            </div>

            

            <div class="space-y-5 p-6">
                <input
                    type="hidden"
                    id="cover_media_id"
                    name="cover_media_id"
                    x-model="selectedMediaId"
                >

                {{-- Selected Preview --}}
                <div
                    x-show="selectedMedia"
                    class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40"
                >
                    <div class="aspect-video bg-slate-950">
                        <img
                            :src="selectedMedia?.url"
                            :alt="selectedMedia?.title || 'รูปภาพหน้าปก'"
                            class="h-full w-full object-cover"
                        >
                    </div>

                    <div class="flex items-start justify-between gap-3 p-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-white" x-text="selectedMedia?.title"></p>
                            <p class="mt-1 text-xs text-slate-500">
                                ID: <span x-text="selectedMedia?.id"></span>
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="selectedMediaId = ''"
                            class="shrink-0 rounded-lg border border-rose-400/20 bg-rose-500/10 px-3 py-2 text-xs font-medium text-rose-300 transition hover:bg-rose-500/20"
                        >
                            ลบรูป
                        </button>
                    </div>
                </div>

                <div
                    x-show="!selectedMedia"
                    class="rounded-2xl border border-dashed border-white/10 bg-slate-950/40 px-4 py-8 text-center"
                >
                    <p class="text-sm font-medium text-slate-300">ยังไม่ได้เลือกรูปหน้าปก</p>
                    <p class="mt-1 text-xs text-slate-500">เลือกรูปจากรายการด้านล่าง</p>
                </div>

                {{-- Search --}}
                <div>
                    <label for="cover_media_search" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ค้นหารูปภาพ
                    </label>
                    <input
                        type="text"
                        id="cover_media_search"
                        x-model="search"
                        placeholder="ค้นหาจากชื่อไฟล์ ชื่อรูป หรือ ID"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                {{-- Media Grid --}}
                <div class="grid max-h-96 grid-cols-2 gap-3 overflow-y-auto pr-1 sm:grid-cols-3">
                    <template x-for="mediaItem in filteredMediaItems" :key="mediaItem.id">
                        <button
                            type="button"
                            @click="selectedMediaId = mediaItem.id"
                            class="group overflow-hidden rounded-xl border text-left transition"
                            :class="String(selectedMediaId) === String(mediaItem.id)
                                ? 'border-blue-400/60 bg-blue-500/10 ring-2 ring-blue-500/20'
                                : 'border-white/10 bg-slate-950/40 hover:border-blue-400/40 hover:bg-white/5'"
                        >
                            <div class="aspect-video bg-slate-950">
                                <img
                                    :src="mediaItem.url"
                                    :alt="mediaItem.title || 'media image'"
                                    class="h-full w-full object-cover transition group-hover:scale-105"
                                >
                            </div>

                            <div class="p-3">
                                <p class="truncate text-xs font-medium text-slate-200" x-text="mediaItem.title"></p>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    ID: <span x-text="mediaItem.id"></span>
                                </p>
                            </div>
                        </button>
                    </template>

                    <div
                        x-show="filteredMediaItems.length === 0"
                        class="col-span-full rounded-xl border border-white/10 bg-slate-950/40 px-4 py-6 text-center text-sm text-slate-500"
                    >
                        ไม่พบรูปภาพที่ตรงกับคำค้นหา
                    </div>
                </div>

                @error('cover_media_id')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- Related Articles --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">บทความที่เกี่ยวข้อง</h2>
            </div>

            <div class="max-h-80 space-y-3 overflow-y-auto p-6 pr-3">
                @forelse ($relatedArticles as $relatedArticle)
                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input
                            type="checkbox"
                            name="related_article_ids[]"
                            value="{{ $relatedArticle->id }}"
                            @checked(in_array($relatedArticle->id, $selectedRelatedArticleIds))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">
                                {{ $relatedArticle->content?->title ?? 'ไม่มีชื่อบทความ' }}
                            </div>
                            <div class="text-xs text-slate-500">
                                #{{ $relatedArticle->id }} | {{ $relatedArticle->content?->slug ?? '-' }}
                            </div>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-slate-500">ยังไม่มีบทความอื่นให้เลือกเป็นบทความที่เกี่ยวข้อง</p>
                @endforelse

                @error('related_article_ids')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
                @error('related_article_ids.*')
                    <p class="text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </section>
    </div>
</div>

<script>
    (() => {
        const storageKey = `papaiwat:article-form-draft:${window.location.pathname}`;

        const getForm = () => document.getElementById('article-form');

        const saveArticleDraft = () => {
            const form = getForm();

            if (!form) {
                return;
            }

            const draft = {
                fields: {},
                checks: {},
            };

            form.querySelectorAll('input, textarea, select').forEach((field) => {
                if (!field.name || field.disabled || field.name === '_token' || field.name === '_method') {
                    return;
                }

                if (field.type === 'checkbox') {
                    draft.checks[field.name] ??= [];

                    if (field.checked) {
                        draft.checks[field.name].push(field.value);
                    }

                    return;
                }

                draft.fields[field.name] = field.value;
            });

            localStorage.setItem(storageKey, JSON.stringify(draft));
        };

        const restoreArticleDraft = () => {
            const form = getForm();

            if (!form) {
                return;
            }

            const rawDraft = localStorage.getItem(storageKey);

            if (!rawDraft) {
                return;
            }

            let draft = {};

            try {
                draft = JSON.parse(rawDraft);
            } catch {
                return;
            }

            form.querySelectorAll('input, textarea, select').forEach((field) => {
                if (!field.name || field.disabled || field.name === '_token' || field.name === '_method') {
                    return;
                }

                if (field.type === 'checkbox') {
                    const values = draft.checks?.[field.name];

                    if (Array.isArray(values)) {
                        field.checked = values.includes(field.value);
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    return;
                }

                if (draft.fields?.[field.name] !== undefined) {
                    field.value = draft.fields[field.name];
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        };

        window.addEventListener('load', () => {
            restoreArticleDraft();

            const form = getForm();

            if (!form) {
                return;
            }

            form.addEventListener('input', saveArticleDraft);
            form.addEventListener('change', saveArticleDraft);

            form.addEventListener('submit', () => {
                localStorage.removeItem(storageKey);
            });
        });
    })();
</script>
