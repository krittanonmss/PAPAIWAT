    {{-- Section: Basic Info --}}
    <section
        x-data="{
            title: window.templeDraft('title', @js(old('title', $content?->title))),
            slug: window.templeDraft('slug', @js(old('slug', $content?->slug))),
            slugEdited: Boolean(window.templeDraft('slug', @js(old('slug', $content?->slug)))),
            makeSlug(value) {
                const thaiMap = {
                    'พระ': 'phra', 'วัด': 'wat', 'ธรรมะ': 'dhamma', 'ธรรม': 'dhamma', 'กรุงเทพ': 'bangkok',
                    'ก': 'k', 'ข': 'kh', 'ค': 'kh', 'ฆ': 'kh', 'ง': 'ng', 'จ': 'ch', 'ฉ': 'ch', 'ช': 'ch', 'ซ': 's',
                    'ญ': 'y', 'ด': 'd', 'ต': 't', 'ถ': 'th', 'ท': 'th', 'ธ': 'th', 'น': 'n', 'บ': 'b', 'ป': 'p',
                    'ผ': 'ph', 'ฝ': 'f', 'พ': 'ph', 'ฟ': 'f', 'ภ': 'ph', 'ม': 'm', 'ย': 'y', 'ร': 'r', 'ล': 'l',
                    'ว': 'w', 'ศ': 's', 'ษ': 's', 'ส': 's', 'ห': 'h', 'อ': 'o', 'ฮ': 'h', 'ะ': 'a', 'ั': 'a',
                    'า': 'a', 'ำ': 'am', 'ิ': 'i', 'ี': 'i', 'ึ': 'ue', 'ื': 'ue', 'ุ': 'u', 'ู': 'u', 'เ': 'e',
                    'แ': 'ae', 'โ': 'o', 'ใ': 'ai', 'ไ': 'ai', '่': '', '้': '', '๊': '', '๋': '', '์': '', '็': '',
                };
                let romanized = value.toString();
                Object.entries(thaiMap).forEach(([thai, latin]) => {
                    romanized = romanized.split(thai).join(latin);
                });

                return romanized
                    .toString()
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            },
            syncSlug() {
                if (! this.slugEdited) {
                    this.slug = this.makeSlug(this.title);
                }
            },
            resetSlug() {
                this.slugEdited = false;
                this.syncSlug();
            }
        }"
        class="temple-panel temple-panel-content overflow-visible rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
    >
        <div id="basic-info" class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ข้อมูลหลักของวัด</h2>
            <p class="mt-1 text-xs text-slate-400">ตั้งชื่อ จัดการ slug เนื้อหาหลัก และ template หน้ารายละเอียด</p>
        </div>

        <div class="space-y-5 p-6">
            <div class="space-y-5">
                <div>
                    <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ชื่อ <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        x-model="title"
                        @input="syncSlug()"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3 text-base font-medium text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('title') border-rose-400 @enderror"
                        placeholder="เช่น พระแก้ว"
                    >
                    @error('title')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-3">
                        <label for="slug" class="block text-sm font-medium text-slate-300">
                            Slug
                        </label>

                        <button
                            type="button"
                            @click="resetSlug()"
                            class="text-xs font-medium text-blue-300 hover:text-blue-200"
                        >
                            สร้างจากชื่ออีกครั้ง
                        </button>
                    </div>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        x-model="slug"
                        @input="slugEdited = true"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 font-mono text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('slug') border-rose-400 @enderror"
                        placeholder="wat-phra-kaew"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        กรอกเองได้ หรือเว้นว่างไว้เพื่อให้ระบบสร้างจากชื่อ ถ้าชื่อเป็นภาษาไทยระบบจะแปลงเป็นตัวอักษรอังกฤษให้
                    </p>

                    @error('slug')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบายสั้น</label>
                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="2"
                    class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('excerpt') border-rose-400 @enderror"
                    placeholder="คำอธิบายสั้นๆ เกี่ยวกับวัด ใช้ในรายการและตัวอย่างเวลาแชร์"
                >{{ old('excerpt', $content?->excerpt) }}</textarea>
                @error('excerpt')
                    <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            @include('admin.content.temples.partials._rich_text_editor', [
                'name' => 'description',
                'id' => 'description',
                'label' => 'รายละเอียด',
                'value' => $content?->description,
                'placeholder' => 'เล่าเนื้อหาหลักของวัด จุดสำคัญ บรรยากาศ และข้อมูลที่ผู้เข้าชมควรรู้',
                'hint' => 'รองรับหัวข้อ ลิสต์ ลิงก์ และข้อความเน้น',
                'minHeight' => '300px',
            ])

            <div class="grid grid-cols-1 gap-5">
                <div class="sm:col-span-2">
                    <label for="template_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                        เทมเพลต หน้า Detail
                    </label>
                    @include('admin.content.partials._searchable_select', [
                        'id' => 'template_id',
                        'name' => 'template_id',
                        'selected' => old('template_id', $content?->template_id),
                        'emptyLabel' => 'ใช้ค่าเริ่มต้นของวัด',
                        'placeholder' => 'เลือกเทมเพลต',
                        'searchPlaceholder' => 'ค้นหาเทมเพลต...',
                        'errorKey' => 'template_id',
                        'visibleLimit' => null,
                        'dataAttributes' => [
                            'data-template-preview-select' => '',
                            'data-preview-target' => 'temple-template-preview',
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
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">
                        ถ้าไม่เลือก ระบบจะใช้เทมเพลต temple-detail ที่เปิดใช้งานอยู่
                    </p>

                </div>

            </div>
        </div>
    </section>
