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
    $bodyEditorValue = $article->body ?? '';

    if (($article->body_format ?? 'html') === 'markdown' && $bodyEditorValue !== '') {
        $bodyEditorValue = (string) \Illuminate\Support\Str::markdown($bodyEditorValue);
    }

    $selectedBodyFormat = old(
        'body_format',
        isset($article) && $article?->body_format ? $article->body_format : 'html'
    );
    $rawBodyValue = old('body', $article->body ?? '');
@endphp

@once
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <style>
        .temple-editor-toolbar.ql-toolbar {
            border: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
            align-items: center;
            font-family: inherit;
        }

        .temple-editor-toolbar.ql-toolbar .ql-formats {
            align-items: center;
            border-right: 1px solid rgb(255 255 255 / 0.1);
            display: inline-flex;
            gap: 0.125rem;
            margin: 0;
            padding-right: 0.375rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-formats:last-child {
            border-right: 0;
            padding-right: 0;
        }

        .temple-editor-toolbar.ql-toolbar button {
            border-radius: 0.5rem;
            color: rgb(203 213 225);
            height: 2rem;
            padding: 0.375rem;
            width: 2rem;
        }

        .temple-editor-toolbar.ql-toolbar button:hover,
        .temple-editor-toolbar.ql-toolbar button:focus,
        .temple-editor-toolbar.ql-toolbar button.ql-active {
            background: rgb(59 130 246 / 0.14);
            color: rgb(147 197 253);
        }

        .temple-editor-toolbar.ql-toolbar .ql-stroke {
            stroke: currentColor;
        }

        .temple-editor-toolbar.ql-toolbar .ql-fill {
            fill: currentColor;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker {
            color: rgb(203 213 225);
            height: 2rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-label {
            align-items: center;
            border: 1px solid rgb(255 255 255 / 0.1);
            border-radius: 0.5rem;
            display: flex;
            min-width: 6.25rem;
            padding-left: 0.625rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-label:hover,
        .temple-editor-toolbar.ql-toolbar .ql-picker-label.ql-active {
            border-color: rgb(96 165 250 / 0.6);
            color: rgb(147 197 253);
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-options {
            border: 1px solid rgb(255 255 255 / 0.12);
            border-radius: 0.75rem;
            background: rgb(15 23 42);
            box-shadow: 0 20px 40px rgb(2 6 23 / 0.45);
            color: rgb(226 232 240);
            margin-top: 0.375rem;
            padding: 0.375rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-item {
            border-radius: 0.5rem;
            padding: 0.375rem 0.625rem;
        }

        .temple-editor-toolbar.ql-toolbar .ql-picker-item:hover,
        .temple-editor-toolbar.ql-toolbar .ql-picker-item.ql-selected {
            background: rgb(59 130 246 / 0.16);
            color: rgb(147 197 253);
        }

        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label::before,
        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item::before {
            content: 'Paragraph';
        }

        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="1"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="1"]::before {
            content: 'Heading 1';
        }

        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="2"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="2"]::before {
            content: 'Heading 2';
        }

        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-label[data-value="3"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-header .ql-picker-item[data-value="3"]::before {
            content: 'Heading 3';
        }

        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label::before,
        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item::before {
            content: 'Normal';
        }

        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="tight"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="tight"]::before {
            content: 'Tight';
        }

        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="relaxed"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="relaxed"]::before {
            content: 'Relaxed';
        }

        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-label[data-value="loose"]::before,
        .temple-editor-toolbar.ql-toolbar .ql-lineheight .ql-picker-item[data-value="loose"]::before {
            content: 'Loose';
        }

        .temple-rich-editor.ql-container {
            border: 0;
            font-family: inherit;
        }

        .temple-rich-editor .ql-editor {
            min-height: inherit;
            padding: 0;
            color: rgb(241 245 249);
            font-size: 1rem;
            line-height: 1.75;
        }

        .temple-rich-editor .ql-editor.ql-blank::before {
            color: rgb(100 116 139);
            font-style: normal;
            left: 0;
            right: 0;
        }

        .temple-rich-editor .ql-editor h1,
        .temple-rich-editor .ql-editor h2,
        .temple-rich-editor .ql-editor h3 {
            margin: 0.875rem 0 0.5rem;
            color: white;
            font-weight: 700;
        }

        .temple-rich-editor .ql-editor h1 {
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .temple-rich-editor .ql-editor h2 {
            font-size: 1.25rem;
            line-height: 1.875rem;
        }

        .temple-rich-editor .ql-editor h3 {
            font-size: 1rem;
            line-height: 1.75rem;
        }

        .temple-rich-editor .ql-editor a {
            color: rgb(147 197 253);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .temple-rich-editor .ql-editor blockquote {
            border-left: 3px solid rgb(96 165 250 / 0.6);
            color: rgb(203 213 225);
            padding-left: 0.875rem;
        }

        .temple-rich-editor .ql-editor .ql-code-block {
            border-radius: 0.75rem;
            background: rgb(2 6 23 / 0.85);
            color: rgb(203 213 225);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            padding: 0.125rem 0.75rem;
        }

        .temple-rich-editor .ql-editor .ql-lineheight-tight {
            line-height: 1.25;
        }

        .temple-rich-editor .ql-editor .ql-lineheight-normal {
            line-height: 1.5;
        }

        .temple-rich-editor .ql-editor .ql-lineheight-relaxed {
            line-height: 1.75;
        }

        .temple-rich-editor .ql-editor .ql-lineheight-loose {
            line-height: 2;
        }

        @for ($i = 1; $i <= 8; $i++)
            .temple-rich-editor .ql-editor .ql-indent-{{ $i }} {
                padding-left: {{ $i * 1.5 }}rem;
            }
        @endfor

        .article-form-ui input[type="text"],
        .article-form-ui input[type="number"],
        .article-form-ui input[type="datetime-local"],
        .article-form-ui input[type="file"],
        .article-form-ui input[type="url"],
        .article-form-ui select,
        .article-form-ui textarea {
            min-height: 3rem;
            font-size: 0.95rem;
        }

        .article-form-ui label {
            font-size: 0.95rem;
        }
    </style>
@endonce

<div class="article-form-ui space-y-6">
    <div class="space-y-6">
        {{-- Main Content --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div id="article-main" class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">ข้อมูลหลักของบทความ</h2>
                <p class="mt-1 text-xs text-slate-400">กรอกชื่อบทความ คำโปรย เนื้อหา ผู้เขียน และเวลาการเผยแพร่ในพื้นที่เดียว</p>
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

                <div
                    class="md:col-span-2"
                    x-data="{ bodyFormat: @js($selectedBodyFormat) }"
                >
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="body_format" class="mb-1.5 block text-sm font-medium text-slate-300">
                                รูปแบบเนื้อหา <span class="text-rose-400">*</span>
                            </label>
                            <select
                                id="body_format"
                                name="body_format"
                                x-model="bodyFormat"
                                class="@error('body_format') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                            >
                                <option value="markdown">Markdown</option>
                                <option value="html">HTML / Rich text</option>
                                <option value="editorjs">EditorJS</option>
                            </select>
                            @error('body_format')
                                <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-xs leading-5 text-slate-400">
                            <span x-show="bodyFormat === 'html'">ใช้ rich text editor และบันทึกเป็น HTML เหมาะกับการจัดหน้าแบบ visual</span>
                            <span x-show="bodyFormat === 'markdown'">ใช้ Markdown เหมาะกับเนื้อหาที่ต้องการแก้ด้วย syntax ตรง ๆ</span>
                            <span x-show="bodyFormat === 'editorjs'">ใช้ EditorJS สำหรับเก็บ JSON หรือ block data ตาม format เดิม</span>
                        </div>
                    </div>

                    <div class="mt-6" x-show="bodyFormat === 'html'">
                        @include('admin.content.partials._rich_text_editor', [
                            'name' => 'body',
                            'id' => 'body',
                            'label' => 'เนื้อหาบทความ',
                            'value' => $bodyEditorValue,
                            'placeholder' => 'เขียนเนื้อหาบทความ จัดหัวข้อ ลิสต์ ลิงก์ และข้อความเน้นได้',
                            'hint' => 'รองรับหัวข้อ ลิสต์ ลิงก์ quote และ code block',
                            'minHeight' => '420px',
                            'maxHeight' => '560px',
                            'disabledExpression' => "bodyFormat !== 'html'",
                        ])
                    </div>

                    <div class="mt-6" x-show="bodyFormat !== 'html'">
                        <label for="body_raw" class="mb-1.5 block text-sm font-medium text-slate-300">
                            เนื้อหาบทความ
                        </label>
                        <textarea
                            id="body_raw"
                            name="body"
                            :disabled="bodyFormat === 'html'"
                            class="@error('body') border-rose-400/60 @else border-white/10 @enderror h-[520px] w-full resize-none overflow-y-auto rounded-xl border bg-slate-950/50 px-4 py-3 font-mono text-sm leading-7 text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                            placeholder="เขียนเนื้อหาตามรูปแบบที่เลือก"
                        >{{ $rawBodyValue }}</textarea>
                        @error('body')
                            <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
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

            </div>
        </section>

        {{-- SEO --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div id="article-seo" class="border-b border-white/10 px-6 py-4">
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
            <div id="article-categories" class="flex flex-col gap-3 border-b border-white/10 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
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

                <div class="max-h-[420px] space-y-3 overflow-y-auto pr-1">
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
            <div id="article-tags" class="flex flex-col gap-3 border-b border-white/10 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
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

                <div class="max-h-[420px] space-y-3 overflow-y-auto pr-1">
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
                mediaSearch: '',
                selectedCover: window.articleDraftMediaId('cover_media_id', @js((string) $selectedCoverMediaId)),
                coverHtml: @js(view('admin.content.articles.partials._cover_media_grid', [
                    'mediaItems' => $coverMediaItems ?? $mediaItems,
                ])->render()),

                init() {
                    this.$watch('selectedCover', () => this.$nextTick(() => window.saveArticleDraft?.()));
                },

                async loadCoverPage(event) {
                    const link = event.target.closest('a');

                    if (!link) {
                        return;
                    }

                    event.preventDefault();

                    const response = await fetch(link.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (response.ok) {
                        this.coverHtml = await response.text();

                        this.$nextTick(() => {
                            window.Alpine.initTree(this.$refs.coverPicker);
                        });
                    }
                },
            }"
            class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        >
            <div id="article-media" class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-white">รูปภาพหน้าปก</h2>
                    <p class="mt-1 text-xs text-slate-400">
                        อัปโหลดรูปใหม่หรือเลือกรูปจาก Media Library เพื่อใช้เป็นภาพหน้าปกของบทความ
                    </p>
                </div>

                <a
                    href="{{ route('admin.media.index') }}"
                    target="_blank"
                    class="shrink-0 rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs text-blue-300 transition hover:bg-blue-500/20"
                >
                    + ไปจัดการมีเดีย
                </a>
            </div>

            <div class="space-y-6 p-6">
                {{-- Quick Upload --}}
                <div
                    x-data="quickArticleMediaUploader()"
                    class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label for="article_quick_media_file" class="mb-1.5 block text-sm font-medium text-slate-300">
                                อัปโหลดรูปหน้าปกใหม่
                            </label>

                            <input
                                id="article_quick_media_file"
                                type="file"
                                accept="image/*"
                                multiple
                                x-ref="fileInput"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white
                                    file:mr-3 file:rounded-lg file:border-0 file:bg-blue-500
                                    file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white
                                    hover:file:bg-blue-600"
                            >

                            <p x-show="errorMessage" x-text="errorMessage" class="mt-1 text-xs text-rose-400"></p>
                            <p class="mt-2 text-xs text-slate-500">
                                เลือกได้หลายรูป ขนาดไม่เกิน 5 MB ต่อรูป ระบบจะบันทึกเข้า Media Library แล้ว refresh หน้า
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="upload()"
                            :disabled="isUploading"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-show="!isUploading">อัปโหลด</span>
                            <span x-show="isUploading">กำลังอัปโหลด...</span>
                        </button>
                    </div>
                </div>

                {{-- Search --}}
                <div class="max-w-md">
                    <label for="cover_media_search" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ค้นหารูปจากชื่อ
                    </label>

                    <input
                        type="text"
                        id="cover_media_search"
                        x-model="mediaSearch"
                        placeholder="พิมพ์ชื่อรูป, title หรือชื่อไฟล์..."
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        ใช้ค้นหาเฉพาะรูปที่แสดงอยู่ในหน้าปัจจุบัน
                    </p>
                </div>

                <input type="hidden" name="cover_media_id" :value="selectedCover">

                <div class="space-y-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-200">รูปหน้าปกบทความ</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            เลือกรูปเดียวสำหรับ card, หน้า list และหน้า detail ของบทความ
                        </p>
                    </div>

                    <div
                        x-ref="coverPicker"
                        x-html="coverHtml"
                        @click="loadCoverPage($event)"
                    ></div>
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

            <div class="max-h-[420px] space-y-3 overflow-y-auto p-6 pr-3">
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

        {{-- Publishing --}}
        <section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div id="article-publishing" class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">การเผยแพร่</h2>
                <p class="mt-1 text-xs text-slate-400">กำหนดสถานะ เวลาเผยแพร่ และการแสดงผลของบทความ</p>
            </div>

            <div class="space-y-6 p-6">
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
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
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
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
    </div>
</div>

<script>
    (() => {
        const storageKey = `papaiwat:article-form-draft:${window.location.pathname}`;
        const hasServerErrors = @json($errors->any());
        const draftStorage = window.sessionStorage;

        const getForm = () => document.getElementById('article-form');

        const readArticleDraft = () => {
            try {
                return JSON.parse(draftStorage.getItem(storageKey)) || {};
            } catch {
                return {};
            }
        };

        window.articleDraftValue = function (name, fallback = '') {
            if (hasServerErrors) {
                return fallback;
            }

            const fields = readArticleDraft().fields || {};

            return Object.prototype.hasOwnProperty.call(fields, name)
                ? fields[name]
                : fallback;
        };

        window.normalizeArticleMediaIds = function (value) {
            const values = Array.isArray(value) ? value : [value];

            return values
                .map((item) => String(item ?? '').trim())
                .filter((item) => /^\d+$/.test(item));
        };

        window.articleDraftMediaId = function (name, fallback = '') {
            const fallbackIds = window.normalizeArticleMediaIds(fallback);
            const draftIds = window.normalizeArticleMediaIds(window.articleDraftValue(name, fallback));

            return draftIds[0] ?? fallbackIds[0] ?? '';
        };

        window.quickArticleMediaUploader = function () {
            return {
                isUploading: false,
                errorMessage: '',

                async upload() {
                    this.errorMessage = '';

                    const files = Array.from(this.$refs.fileInput.files || []);

                    if (files.length === 0) {
                        this.errorMessage = 'กรุณาเลือกรูปก่อนอัปโหลด';
                        return;
                    }

                    const maxFileSize = 5 * 1024 * 1024;
                    const invalidFile = files.find((file) => !file.type.startsWith('image/'));
                    const oversizedFile = files.find((file) => file.size > maxFileSize);

                    if (invalidFile) {
                        this.errorMessage = 'อัปโหลดได้เฉพาะไฟล์รูปภาพเท่านั้น';
                        return;
                    }

                    if (oversizedFile) {
                        this.errorMessage = `ไฟล์ ${oversizedFile.name} มีขนาดเกิน 5 MB`;
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('visibility', 'public');
                    files.forEach((file) => formData.append('files[]', file));

                    this.isUploading = true;

                    try {
                        const response = await fetch('{{ route('admin.media.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            const payload = await response.json().catch(() => null);
                            this.errorMessage = payload?.message || 'อัปโหลดไม่สำเร็จ กรุณาตรวจสอบไฟล์อีกครั้ง';
                            return;
                        }

                        window.location.reload();
                    } catch (error) {
                        this.errorMessage = 'เกิดข้อผิดพลาดระหว่างอัปโหลด';
                    } finally {
                        this.isUploading = false;
                    }
                },
            };
        };

        const registerArticleRichTextFormats = () => {
            if (window.articleRichTextFormatsRegistered || !window.Quill) {
                return;
            }

            const Parchment = window.Quill.import('parchment');
            const LineHeight = new Parchment.ClassAttributor('lineheight', 'ql-lineheight', {
                scope: Parchment.Scope.BLOCK,
                whitelist: ['tight', 'normal', 'relaxed', 'loose'],
            });

            window.Quill.register(LineHeight, true);
            window.articleRichTextFormatsRegistered = true;
        };

        const initArticleRichEditors = () => {
            if (!window.Quill) {
                return;
            }

            registerArticleRichTextFormats();

            document.querySelectorAll('[data-rich-editor]').forEach((wrapper) => {
                if (wrapper.dataset.richEditorBound === 'true') {
                    return;
                }

                const input = wrapper.querySelector('[data-rich-editor-input]');
                const editorBody = wrapper.querySelector('[data-editor-body]');
                const sourceEditor = wrapper.querySelector('[data-editor-source]');
                const sourceToggle = wrapper.querySelector('[data-editor-source-toggle]');
                const toolbar = wrapper.querySelector('[data-editor-toolbar]');
                const counter = wrapper.querySelector('[data-editor-count]');
                const modeLabel = wrapper.querySelector('[data-editor-mode-label]');

                if (!input || !editorBody || !toolbar) {
                    return;
                }

                wrapper.dataset.richEditorBound = 'true';

                const quill = new Quill(editorBody, {
                    theme: 'snow',
                    placeholder: wrapper.dataset.placeholder || '',
                    modules: {
                        history: {
                            delay: 1000,
                            maxStack: 100,
                            userOnly: true,
                        },
                        toolbar,
                    },
                    formats: [
                        'blockquote',
                        'bold',
                        'code-block',
                        'header',
                        'indent',
                        'italic',
                        'lineheight',
                        'link',
                        'list',
                        'script',
                        'strike',
                        'underline',
                    ],
                });

                quill.root.innerHTML = input.value || '';
                input._quill = quill;
                if (sourceEditor) {
                    sourceEditor.value = input.value || '';
                }

                const dispatchValueChange = () => {
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                };

                const updateCounter = () => {
                    if (counter) {
                        counter.textContent = `${Math.max(quill.getLength() - 1, 0).toLocaleString()} ตัวอักษร`;
                    }
                };

                const syncValue = () => {
                    const html = quill.root.innerHTML.trim();
                    input.value = html === '<p><br></p>' ? '' : html;

                    if (sourceEditor && sourceEditor.classList.contains('hidden')) {
                        sourceEditor.value = input.value;
                    }

                    updateCounter();
                    dispatchValueChange();
                };

                quill.on('text-change', syncValue);
                updateCounter();

                if (sourceEditor) {
                    sourceEditor.addEventListener('input', () => {
                        input.value = sourceEditor.value.trim();
                        dispatchValueChange();
                    });
                }

                if (sourceToggle && sourceEditor) {
                    sourceToggle.addEventListener('click', () => {
                        const sourceIsHidden = sourceEditor.classList.contains('hidden');

                        if (sourceIsHidden) {
                            sourceEditor.value = input.value;
                            editorBody.classList.add('hidden');
                            sourceEditor.classList.remove('hidden');
                            sourceToggle.classList.add('text-blue-300');
                            if (modeLabel) {
                                modeLabel.textContent = 'HTML source';
                            }
                            return;
                        }

                        input.value = sourceEditor.value.trim();
                        quill.root.innerHTML = input.value || '';
                        sourceEditor.classList.add('hidden');
                        editorBody.classList.remove('hidden');
                        sourceToggle.classList.remove('text-blue-300');
                        if (modeLabel) {
                            modeLabel.textContent = 'Rich text';
                        }
                        updateCounter();
                        dispatchValueChange();
                    });
                }
            });
        };

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

            draftStorage.setItem(storageKey, JSON.stringify(draft));
        };

        window.saveArticleDraft = saveArticleDraft;

        const restoreArticleDraft = () => {
            const form = getForm();

            if (!form) {
                return;
            }

            if (hasServerErrors) {
                return;
            }

            const rawDraft = draftStorage.getItem(storageKey);

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

                    if (field.matches('[data-rich-editor-input]') && field._quill) {
                        field._quill.root.innerHTML = field.value || '';
                        const sourceEditor = field
                            .closest('[data-rich-editor]')
                            ?.querySelector('[data-editor-source]');

                        if (sourceEditor) {
                            sourceEditor.value = field.value || '';
                        }
                    }

                    field.dispatchEvent(new Event('input', { bubbles: true }));
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

        };

        window.addEventListener('load', () => {
            initArticleRichEditors();
            restoreArticleDraft();

            const form = getForm();

            if (!form) {
                return;
            }

            form.addEventListener('input', saveArticleDraft);
            form.addEventListener('change', saveArticleDraft);

            form.addEventListener('submit', () => {
                initArticleRichEditors();
                draftStorage.removeItem(storageKey);
            });
        });
    })();
</script>
