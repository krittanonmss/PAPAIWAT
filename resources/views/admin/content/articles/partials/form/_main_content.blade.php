        {{-- Main Content --}}
        <section class="article-panel article-panel-content overflow-visible rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div id="article-main" class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">ข้อมูลหลักของบทความ</h2>
                <p class="mt-1 text-xs text-slate-400">กรอกชื่อ คำโปรย เนื้อหา ผู้เขียน และเวลาการเผยแพร่ในพื้นที่เดียว</p>
            </div>

            <div class="grid gap-6 p-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ชื่อ <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $content->title ?? '') }}"
                        class="@error('title') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="กรอกชื่อ"
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
                        เทมเพลต หน้า Detail
                    </label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'template_id',
                        'name' => 'template_id',
                        'selected' => old('template_id', $content?->template_id),
                        'emptyLabel' => 'ใช้ค่าเริ่มต้นของบทความ',
                        'placeholder' => 'เลือกเทมเพลต',
                        'searchPlaceholder' => 'ค้นหาเทมเพลต...',
                        'errorKey' => 'template_id',
                        'visibleLimit' => null,
                        'dataAttributes' => [
                            'data-template-preview-select' => '',
                            'data-preview-target' => 'article-template-preview',
                            'data-preview-base' => $templatePreviewUrl,
                        ],
                        'options' => $detailTemplates->map(fn ($template) => [
                            'value' => $template->id,
                            'label' => $template->name,
                            'meta' => $template->key,
                            'search' => $template->name . ' ' . $template->key . ' ' . $template->view_path,
                        ]),
                    ])
                    @error('template_id')
                        <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-500">
                        ถ้าไม่เลือก ระบบจะใช้เทมเพลต article-detail ที่เปิดใช้งานอยู่
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label for="title_en" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ชื่อภาษาอังกฤษ
                    </label>
                    <input
                        type="text"
                        id="title_en"
                        name="title_en"
                        value="{{ old('title_en', $article->title_en ?? '') }}"
                        class="@error('title_en') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="กรอกชื่อภาษาอังกฤษ"
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
                        placeholder="สรุปแบบสั้น"
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
                        placeholder="สรุปภาษาอังกฤษแบบสั้น"
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
                            'label' => 'เนื้อหา',
                            'value' => $bodyEditorValue,
                            'placeholder' => 'เขียนเนื้อหา จัดหัวข้อ ลิสต์ ลิงก์ และข้อความเน้นได้',
                            'hint' => 'รองรับหัวข้อ ลิสต์ ลิงก์ quote และ code block',
                            'minHeight' => '420px',
                            'maxHeight' => '560px',
                            'disabledExpression' => "bodyFormat !== 'html'",
                        ])
                    </div>

                    <div class="mt-6" x-show="bodyFormat !== 'html'">
                        <label for="body_raw" class="mb-1.5 block text-sm font-medium text-slate-300">
                            เนื้อหา
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
