<div class="space-y-6" x-data="mediaCreatePreview()">
    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ไฟล์สื่อ</h2>
            <p class="mt-1 text-xs text-slate-400">เลือกไฟล์และตรวจ preview ก่อนอัปโหลด</p>
        </div>

        <div class="grid gap-5 p-6 xl:grid-cols-[minmax(260px,0.82fr)_minmax(340px,1.18fr)]">
            <div class="space-y-5">
                <div class="rounded-2xl border border-dashed border-blue-400/30 bg-blue-500/5 p-4">
                    <label for="file" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ไฟล์ <span class="text-rose-400">*</span>
                    </label>

                    <input
                        type="file"
                        id="file"
                        name="files[]"
                        multiple
                        required
                        x-ref="fileInput"
                        @change="previewFiles($event)"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-slate-300 outline-none transition file:mr-3 file:rounded-xl file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        เลือกได้หลายไฟล์ รองรับรูปภาพและไฟล์ทั่วไป ขนาดไม่เกิน 5 MB ต่อไฟล์
                    </p>

                    @error('file')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                    @error('files')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                    @error('files.*')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="media_folder_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                        โฟลเดอร์
                    </label>

                    @include('admin.content.partials._async_select', [
                        'id' => 'media_folder_id',
                        'name' => 'media_folder_id',
                        'selected' => old('media_folder_id'),
                        'searchUrl' => route('admin.lookups.media-folders'),
                        'placeholder' => 'ค้นหาโฟลเดอร์',
                        'searchPlaceholder' => 'ค้นหาชื่อ / slug / ID',
                        'emptyLabel' => 'ไม่มีโฟลเดอร์',
                    ])

                    @error('media_folder_id')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-white">Preview</h3>
                        <p class="mt-1 text-xs text-slate-500">แสดงตัวอย่างไฟล์ที่กำลังจะอัปโหลด</p>
                    </div>

                    <button
                        type="button"
                        x-show="files.length > 0"
                        @click="clearFiles()"
                        class="rounded-xl border border-white/10 px-3 py-1.5 text-xs text-slate-300 transition hover:bg-white/10"
                    >
                        ล้างไฟล์
                    </button>
                </div>

                <template x-if="files.length === 0">
                    <div class="flex min-h-[260px] items-center justify-center rounded-2xl border border-dashed border-white/10 bg-slate-950/60 text-sm text-slate-500">
                        ยังไม่ได้เลือกไฟล์
                    </div>
                </template>

                <div x-show="files.length > 0" class="grid max-h-[560px] gap-3 overflow-y-auto pr-1 sm:grid-cols-2 2xl:grid-cols-3">
                    <template x-for="file in files" :key="file.key">
                        <article class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/70">
                            <div class="aspect-[4/3] bg-slate-950">
                                <template x-if="file.isImage">
                                    <img :src="file.url" :alt="file.name" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!file.isImage">
                                    <div class="flex h-full w-full items-center justify-center">
                                        <span class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-sm font-semibold text-slate-300" x-text="file.extension"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="space-y-1 border-t border-white/10 p-3">
                                <p class="truncate text-sm font-medium text-white" x-text="file.name"></p>
                                <p class="text-xs text-slate-500" x-text="file.size"></p>
                            </div>
                        </article>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ข้อมูลแสดงผล</h2>
            <p class="mt-1 text-xs text-slate-400">ข้อมูลที่ใช้แสดงในระบบ หน้าเว็บไซต์ และ SEO</p>
        </div>

        <div class="grid gap-5 p-6 lg:grid-cols-[minmax(0,1fr)_240px]">
            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ชื่อแสดงผล
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="เช่น รูปปกพระแก้ว"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-600 transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >

                @error('title')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="visibility" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Visibility
                </label>

                <select
                    id="visibility"
                    name="visibility"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >
                    <option value="public" class="bg-slate-900" @selected(old('visibility', 'public') === 'public')>สาธารณะ</option>
                    <option value="private" class="bg-slate-900" @selected(old('visibility') === 'private')>ส่วนตัว</option>
                </select>

                @error('visibility')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                <label for="alt_text" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Alt Text
                </label>

                <input
                    type="text"
                    id="alt_text"
                    name="alt_text"
                    value="{{ old('alt_text') }}"
                    placeholder="คำอธิบายภาพสำหรับ accessibility"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-600 transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >

                <p class="mt-1 text-xs text-slate-500">
                    แนะนำให้กรอกเมื่ออัปโหลดรูปที่จะใช้บนหน้าเว็บไซต์
                </p>

                @error('alt_text')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">รายละเอียดเพิ่มเติม</h2>
            <p class="mt-1 text-xs text-slate-400">คำบรรยายและรายละเอียดของไฟล์</p>
        </div>

        <div class="grid gap-5 p-6 xl:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]">
            <div>
                <label for="caption" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Caption
                </label>

                <textarea
                    id="caption"
                    name="caption"
                    rows="3"
                    placeholder="คำบรรยายสั้นของไฟล์"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-600 transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >{{ old('caption') }}</textarea>

                @error('caption')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">
                    คำอธิบาย
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="รายละเอียดเพิ่มเติมของไฟล์"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-600 transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>
</div>

@once
    <script>
        function mediaCreatePreview() {
            return {
                files: [],

                formatSize(bytes) {
                    if (!bytes) {
                        return '0 KB';
                    }

                    if (bytes >= 1024 * 1024) {
                        return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
                    }

                    return `${(bytes / 1024).toFixed(1)} KB`;
                },

                previewFiles(event) {
                    this.files.forEach((file) => {
                        if (file.url) {
                            URL.revokeObjectURL(file.url);
                        }
                    });

                    this.files = Array.from(event.target.files || []).map((file, index) => {
                        const extension = (file.name.split('.').pop() || 'FILE').toUpperCase();
                        const isImage = file.type.startsWith('image/');

                        return {
                            key: `${file.name}-${file.size}-${index}`,
                            name: file.name,
                            size: this.formatSize(file.size),
                            extension,
                            isImage,
                            url: isImage ? URL.createObjectURL(file) : null,
                        };
                    });
                },

                clearFiles() {
                    this.files.forEach((file) => {
                        if (file.url) {
                            URL.revokeObjectURL(file.url);
                        }
                    });

                    this.files = [];
                    this.$refs.fileInput.value = '';
                },
            };
        }
    </script>
@endonce
