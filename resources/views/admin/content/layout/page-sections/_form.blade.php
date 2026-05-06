@php
    $decodeOldJson = function (string $key, array $fallback = []) {
        $value = old($key);

        if (! is_string($value) || $value === '') {
            return $fallback;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $fallback;
    };

    $initialContent = $decodeOldJson('content', $section->content ?? []);
    $initialSettings = $decodeOldJson('settings', $section->settings ?? []);
    $initialComponent = old('component_key', $section->component_key ?? 'hero');
    $initialStatus = old('status', $section->status ?? 'active');
    $initialVisible = (bool) old('is_visible', $section->is_visible ?? true);
    $sectionName = old('name', $section->name ?? '');
    $sectionKey = old('section_key', $section->section_key ?? '');

    $blocks = [
        'hero' => ['label' => 'Hero', 'description' => 'หัวหน้าใหญ่พร้อมปุ่ม'],
        'rich_text' => ['label' => 'ข้อความ', 'description' => 'หัวข้อและเนื้อหายาว'],
        'image_text' => ['label' => 'รูป + ข้อความ', 'description' => 'วางภาพคู่กับรายละเอียด'],
        'cta' => ['label' => 'ปุ่มเชิญชวน', 'description' => 'ข้อความสั้นพร้อมปุ่ม'],
        'article_grid' => ['label' => 'รายการบทความ', 'description' => 'ดึงบทความมาแสดงอัตโนมัติ'],
        'temple_grid' => ['label' => 'รายการวัด', 'description' => 'ดึงวัดมาแสดงอัตโนมัติ'],
        'article_list_full' => ['label' => 'หน้ารวมบทความ', 'description' => 'list เต็มพร้อมค้นหาและแบ่งหน้า'],
        'temple_list_full' => ['label' => 'หน้ารวมวัด', 'description' => 'list เต็มพร้อมตัวกรองและแบ่งหน้า'],
        'gallery' => ['label' => 'แกลเลอรี', 'description' => 'รวมรูปหลายรูปจาก URL'],
        'faq' => ['label' => 'FAQ', 'description' => 'คำถามคำตอบที่พบบ่อย'],
        'stats' => ['label' => 'ตัวเลขสำคัญ', 'description' => 'สถิติหรือจุดเด่นแบบสั้น'],
        'contact' => ['label' => 'ข้อมูลติดต่อ', 'description' => 'ที่อยู่ เบอร์โทร และอีเมล'],
    ];
@endphp

<div
    class="space-y-6"
    x-data="{
        component: @js($initialComponent),
        content: {
            eyebrow: @js($initialContent['eyebrow'] ?? ''),
            title: @js($initialContent['title'] ?? ''),
            subtitle: @js($initialContent['subtitle'] ?? ''),
            body: @js($initialContent['body'] ?? ''),
            primary_label: @js($initialContent['primary_label'] ?? ''),
            primary_url: @js($initialContent['primary_url'] ?? ''),
            secondary_label: @js($initialContent['secondary_label'] ?? ''),
            secondary_url: @js($initialContent['secondary_url'] ?? ''),
            image_media_id: @js((string) ($initialContent['image_media_id'] ?? '')),
            image_url: @js($initialContent['image_url'] ?? ''),
            gallery_text: @js($initialContent['gallery_text'] ?? ''),
            faq_text: @js($initialContent['faq_text'] ?? ''),
            stats_text: @js($initialContent['stats_text'] ?? ''),
            phone: @js($initialContent['phone'] ?? ''),
            email: @js($initialContent['email'] ?? ''),
            address: @js($initialContent['address'] ?? ''),
            map_url: @js($initialContent['map_url'] ?? '')
        },
        settings: {
            background: @js($initialSettings['background'] ?? 'dark'),
            align: @js($initialSettings['align'] ?? 'center'),
            layout: @js($initialSettings['layout'] ?? 'image_right'),
            source: @js($initialSettings['source'] ?? 'featured'),
            limit: @js((int) ($initialSettings['limit'] ?? 4))
        },
        mediaSearch: '',
        selectedImage: @js((string) ($initialContent['image_media_id'] ?? '')),
        mediaHtml: @js(view('admin.content.layout.page-sections.partials._media_grid', [
            'mediaItems' => $sectionMediaItems,
        ])->render()),
        isUploading: false,
        uploadError: '',
        pick(type) {
            this.component = type;
            if (! this.content.title) {
                const labels = {
                    hero: 'หัวข้อหลักของหน้า',
                    rich_text: 'หัวข้อเนื้อหา',
                    image_text: 'หัวข้อพร้อมรูปภาพ',
                    cta: 'พร้อมเริ่มต้นใช้งาน',
                    article_grid: 'บทความแนะนำ',
                    temple_grid: 'วัดแนะนำ',
                    article_list_full: 'บทความทั้งหมด',
                    temple_list_full: 'รวมวัดทั่วไทย',
                    gallery: 'แกลเลอรี',
                    faq: 'คำถามที่พบบ่อย',
                    stats: 'ตัวเลขสำคัญ',
                    contact: 'ติดต่อเรา'
                };
                this.content.title = labels[type] || '';
            }
        },
        selectImage(mediaId) {
            this.selectedImage = mediaId;
            this.content.image_media_id = mediaId;
            if (mediaId) {
                this.content.image_url = '';
            }
        },
        async loadMediaPage(event) {
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
                this.mediaHtml = await response.text();

                this.$nextTick(() => {
                    window.Alpine.initTree(this.$refs.sectionMediaPicker);
                });
            }
        },
        async uploadImage() {
            this.uploadError = '';

            const file = this.$refs.sectionFileInput.files[0];

            if (!file) {
                this.uploadError = 'กรุณาเลือกรูปก่อนอัปโหลด';
                return;
            }

            const formData = new FormData();
            formData.append('_token', @js(csrf_token()));
            formData.append('file', file);
            formData.append('visibility', 'public');

            this.isUploading = true;

            try {
                const response = await fetch(@js(route('admin.media.store')), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    this.uploadError = 'อัปโหลดไม่สำเร็จ กรุณาตรวจสอบไฟล์อีกครั้ง';
                    return;
                }

                window.location.reload();
            } catch (error) {
                this.uploadError = 'เกิดข้อผิดพลาดระหว่างอัปโหลด';
            } finally {
                this.isUploading = false;
            }
        },
    }"
>
    <input type="hidden" name="component_key" x-model="component">
    <input type="hidden" name="content" :value="JSON.stringify(content)">
    <input type="hidden" name="settings" :value="JSON.stringify(settings)">
    <input type="hidden" name="section_key" value="{{ $sectionKey }}">

    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-5">
            <p class="text-sm font-medium text-blue-300">Page Builder</p>
            <h2 class="mt-1 text-lg font-semibold text-white">เลือก block ที่ต้องการแสดง</h2>
            <p class="mt-1 text-sm text-slate-400">เลือกชนิด block แล้วกรอกเฉพาะข้อความ รูป และปุ่มที่ต้องใช้</p>
        </div>

        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach($blocks as $key => $block)
                <button
                    type="button"
                    class="rounded-2xl border p-4 text-left transition"
                    :class="component === @js($key) ? 'border-blue-400/60 bg-blue-500/15 text-white' : 'border-white/10 bg-slate-950/40 text-slate-300 hover:border-blue-400/30 hover:bg-white/[0.06]'"
                    @click="pick(@js($key))"
                >
                    <span class="block text-sm font-semibold">{{ $block['label'] }}</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-400">{{ $block['description'] }}</span>
                </button>
            @endforeach
        </div>

        @error('component_key')
            <p class="mt-3 text-sm text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-5">
            <h2 class="text-lg font-semibold text-white">เนื้อหา block</h2>
            <p class="mt-1 text-sm text-slate-400">ช่องที่ไม่เกี่ยวกับ block ที่เลือกจะถูกซ่อนไว้</p>
        </div>

        <div class="space-y-5">
            <div x-show="['hero', 'image_text', 'rich_text', 'cta', 'article_grid', 'temple_grid', 'article_list_full', 'temple_list_full', 'gallery', 'faq', 'stats', 'contact'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความเล็กด้านบน</label>
                <input
                    type="text"
                    x-model="content.eyebrow"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น PAPAIWAT"
                >
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">หัวข้อหลัก</label>
                <input
                    type="text"
                    x-model="content.title"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="หัวข้อที่จะแสดงบนหน้าเว็บ"
                >
            </div>

            <div x-show="!['cta', 'contact'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบายสั้น</label>
                <textarea
                    x-model="content.subtitle"
                    rows="3"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm leading-6 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="คำอธิบายสั้น ๆ ใต้หัวข้อ"
                ></textarea>
            </div>

            <div x-show="['rich_text', 'image_text', 'cta'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">เนื้อหา</label>
                <textarea
                    x-model="content.body"
                    rows="8"
                    class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-7 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="พิมพ์เนื้อหาได้เลย ระบบจะแสดงเป็นย่อหน้าให้อัตโนมัติ"
                ></textarea>
            </div>

            <div x-show="['hero', 'image_text'].includes(component)" x-cloak class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-5">
                <div>
                    <h3 class="text-sm font-semibold text-white">รูปภาพของ Block</h3>
                    <p class="mt-1 text-xs text-slate-500">อัปโหลดรูปใหม่หรือเลือกรูปจาก Media Library เหมือนหน้า temple/article</p>
                </div>

                <div class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label class="mb-1.5 block text-sm font-medium text-slate-300">อัปโหลดรูปใหม่</label>
                            <input
                                type="file"
                                accept="image/*"
                                x-ref="sectionFileInput"
                                class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white file:mr-3 file:rounded-lg file:border-0 file:bg-blue-500 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-blue-600"
                            >
                            <p x-show="uploadError" x-text="uploadError" class="mt-1 text-xs text-rose-400"></p>
                            <p class="mt-2 text-xs text-slate-500">รูปจะถูกบันทึกเข้า Media Library แล้ว refresh หน้าเพื่อให้เลือกรูปได้ทันที</p>
                        </div>

                        <button
                            type="button"
                            @click="uploadImage()"
                            :disabled="isUploading"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-show="!isUploading">อัปโหลด</span>
                            <span x-show="isUploading">กำลังอัปโหลด...</span>
                        </button>
                    </div>
                </div>

                <div class="max-w-md">
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ค้นหารูปจากชื่อ</label>
                    <input
                        type="text"
                        x-model="mediaSearch"
                        placeholder="พิมพ์ชื่อรูป, title หรือชื่อไฟล์..."
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                    <p class="mt-1 text-xs text-slate-500">ใช้ค้นหาเฉพาะรูปที่แสดงอยู่ในหน้าปัจจุบัน</p>
                </div>

                <div
                    x-ref="sectionMediaPicker"
                    x-html="mediaHtml"
                    @click="loadMediaPage($event)"
                ></div>
            </div>

            <div x-show="component === 'gallery'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">รายการรูปภาพ</label>
                <textarea
                    x-model="content.gallery_text"
                    rows="7"
                    class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-7 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="ใส่ 1 รูปต่อ 1 บรรทัด เช่น&#10;https://example.com/image.jpg | คำอธิบายรูป"
                ></textarea>
                <p class="mt-1 text-xs text-slate-500">รูปแบบ: URL รูป | คำอธิบายรูป</p>
            </div>

            <div x-show="component === 'faq'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">คำถามคำตอบ</label>
                <textarea
                    x-model="content.faq_text"
                    rows="8"
                    class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-7 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="ใส่ 1 คำถามต่อ 1 บรรทัด เช่น&#10;เปิดกี่โมง? | เปิดทุกวัน 08:00-17:00"
                ></textarea>
                <p class="mt-1 text-xs text-slate-500">รูปแบบ: คำถาม | คำตอบ</p>
            </div>

            <div x-show="component === 'stats'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">ตัวเลขสำคัญ</label>
                <textarea
                    x-model="content.stats_text"
                    rows="6"
                    class="max-h-80 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-7 text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="ใส่ 1 รายการต่อ 1 บรรทัด เช่น&#10;120+ | วัดในระบบ"
                ></textarea>
                <p class="mt-1 text-xs text-slate-500">รูปแบบ: ตัวเลข | คำอธิบาย</p>
            </div>

            <div x-show="component === 'contact'" x-cloak class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">เบอร์โทร</label>
                    <input type="text" x-model="content.phone" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">อีเมล</label>
                    <input type="text" x-model="content.email" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ที่อยู่</label>
                    <textarea x-model="content.address" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"></textarea>
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ลิงก์แผนที่</label>
                    <input type="text" x-model="content.map_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="https://maps.google.com/...">
                </div>
            </div>

            <div x-show="['hero', 'image_text'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">URL รูปภาพสำรอง</label>
                <input
                    type="text"
                    x-model="content.image_url"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="https://... หรือ /storage/..."
                >
            </div>

            <div x-show="['hero', 'cta'].includes(component)" x-cloak class="grid gap-5 lg:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มหลัก</label>
                    <input type="text" x-model="content.primary_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น ดูเพิ่มเติม">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ลิงก์ปุ่มหลัก</label>
                    <input type="text" x-model="content.primary_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น /temple-list">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความปุ่มรอง</label>
                    <input type="text" x-model="content.secondary_label" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น อ่านบทความ">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">ลิงก์ปุ่มรอง</label>
                    <input type="text" x-model="content.secondary_url" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เช่น /article-list">
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-5">
            <h2 class="text-lg font-semibold text-white">การแสดงผล</h2>
            <p class="mt-1 text-sm text-slate-400">ปรับหน้าตาและแหล่งข้อมูลแบบเลือกจากรายการ</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">พื้นหลัง</label>
                <select x-model="settings.background" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="dark" class="bg-slate-900">เข้ม</option>
                    <option value="soft" class="bg-slate-900">นุ่ม</option>
                    <option value="plain" class="bg-slate-900">เรียบ</option>
                </select>
            </div>

            <div x-show="component === 'image_text'" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">ตำแหน่งรูป</label>
                <select x-model="settings.layout" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="image_right" class="bg-slate-900">รูปอยู่ขวา</option>
                    <option value="image_left" class="bg-slate-900">รูปอยู่ซ้าย</option>
                </select>
            </div>

            <div x-show="['article_grid', 'temple_grid', 'article_list_full', 'temple_list_full'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">แหล่งข้อมูล</label>
                <select x-model="settings.source" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="featured" class="bg-slate-900">รายการแนะนำ</option>
                    <option value="popular" class="bg-slate-900">ยอดนิยม</option>
                    <option value="all" class="bg-slate-900">ล่าสุดทั้งหมด</option>
                </select>
            </div>

            <div x-show="['article_grid', 'temple_grid'].includes(component)" x-cloak>
                <label class="mb-1.5 block text-sm font-medium text-slate-300">จำนวนที่แสดง</label>
                <input type="number" min="1" max="12" x-model.number="settings.limit" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
            </div>

            <div>
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">ลำดับบนหน้า</label>
                <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', $section->sort_order ?? 0) }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                @error('sort_order')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">สถานะ</label>
                <select id="status" name="status" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                    <option value="active" class="bg-slate-900" @selected($initialStatus === 'active')>เปิดใช้งาน</option>
                    <option value="inactive" class="bg-slate-900" @selected($initialStatus === 'inactive')>ปิดใช้งาน</option>
                </select>
            </div>

            <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4 lg:col-span-2">
                <div class="flex items-start gap-3">
                    <input id="is_visible" type="checkbox" name="is_visible" value="1" class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20" @checked($initialVisible)>
                    <div>
                        <label for="is_visible" class="text-sm font-medium text-white">แสดง block นี้บนหน้าเว็บ</label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">ถ้าปิดไว้ block นี้จะยังอยู่ในระบบ แต่ไม่แสดงบนหน้า frontend</p>
                    </div>
                </div>
            </div>
        </div>

        <details class="mt-5 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
            <summary class="cursor-pointer text-sm font-medium text-slate-300">ตั้งค่าขั้นสูง</summary>
            <div class="mt-4 grid gap-5 lg:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">ชื่อ block ในระบบ</label>
                    <input id="name" type="text" name="name" value="{{ $sectionName }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20" placeholder="เว้นว่างได้">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-300">รหัส block</label>
                    <input type="text" value="{{ $sectionKey ?: 'ระบบจะสร้างให้อัตโนมัติ' }}" disabled class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-slate-500">
                </div>
            </div>
        </details>
    </div>
</div>
