<div class="space-y-6">
    {{-- Page Information --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <p class="text-sm font-medium text-blue-300">ตัวสร้างหน้า</p>
            <h2 class="mt-1 text-lg font-semibold text-white">ข้อมูลหน้า</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อมูลหลักของหน้าเว็บไซต์</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ชื่อหน้า <span class="text-rose-400">*</span>
                </label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $page->title ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น หน้าแรก"
                    required
                >
                @error('title')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="hidden">
                <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-300">
                    URL ของหน้า
                </label>
                <input
                    id="slug"
                    type="text"
                    name="slug"
                    value="{{ old('slug', $page->slug ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น about"
                >
                <p class="mt-1.5 text-xs text-slate-500">เว้นว่างได้ ระบบจะสร้างจากชื่อหน้าให้อัตโนมัติ</p>
                @error('slug')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="template_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                    รูปแบบหน้า
                </label>
                <select
                    id="template_id"
                    name="template_id"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                    <option value="">Page Builder / เริ่มต้น</option>
                    @foreach($templates as $template)
                        <option
                            value="{{ $template->id }}"
                            {{ old('template_id', $page->template_id ?? '') == $template->id ? 'selected' : '' }}
                        >
                            {{ $template->name }}
                        </option>
                    @endforeach
                </select>
                @error('template_id')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="hidden">
                <label for="page_type" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ประเภทหน้า <span class="text-rose-400">*</span>
                </label>
                <input
                    id="page_type"
                    type="text"
                    name="page_type"
                    value="{{ old('page_type', $page->page_type ?? 'custom') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="custom"
                    required
                >
                @error('page_type')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                    สถานะ <span class="text-rose-400">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    required
                >
                    @foreach(['draft' => 'ฉบับร่าง', 'published' => 'เผยแพร่', 'archived' => 'เก็บถาวร'] as $status => $label)
                        <option
                            value="{{ $status }}"
                            {{ old('status', $page->status ?? 'draft') === $status ? 'selected' : '' }}
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="hidden">
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ลำดับ
                </label>
                <input
                    id="sort_order"
                    type="number"
                    name="sort_order"
                    min="0"
                    value="{{ old('sort_order', $page->sort_order ?? 0) }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('sort_order')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4 lg:col-span-2">
                <div class="flex items-start gap-3">
                    <input
                        id="is_homepage"
                        type="checkbox"
                        name="is_homepage"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20"
                        {{ old('is_homepage', $page->is_homepage ?? false) ? 'checked' : '' }}
                    >
                    <div>
                        <label for="is_homepage" class="text-sm font-medium text-white">
                            ตั้งเป็นหน้าแรก
                        </label>
                        <p class="mt-1 text-xs text-slate-400">ถ้าเลือกหน้านี้ หน้า homepage เดิมจะถูกยกเลิก</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Summary --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <p class="text-sm font-medium text-blue-300">เนื้อหาหน้า</p>
            <h2 class="mt-1 text-lg font-semibold text-white">สรุปเนื้อหา</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อความสรุปและรายละเอียดของหน้า</p>
        </div>

        <div class="space-y-5">
            <div>
                <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-300">คำโปรย</label>
                <input
                    id="excerpt"
                    type="text"
                    name="excerpt"
                    value="{{ old('excerpt', $page->excerpt ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="คำอธิบายสั้น ๆ ของหน้า"
                >
                @error('excerpt')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบาย</label>
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="รายละเอียดเพิ่มเติมของหน้า"
                >{{ old('description', $page->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- SEO Settings --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <p class="text-sm font-medium text-blue-300">การค้นหาและการแชร์</p>
            <h2 class="mt-1 text-lg font-semibold text-white">ตั้งค่า SEO</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อมูลสำหรับเครื่องมือค้นหาและการแชร์บนโซเชียล</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-300">ชื่อ SEO</label>
                <input
                    id="meta_title"
                    type="text"
                    name="meta_title"
                    value="{{ old('meta_title', $page->meta_title ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('meta_title')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="meta_keywords" class="mb-1.5 block text-sm font-medium text-slate-300">คีย์เวิร์ด SEO</label>
                <input
                    id="meta_keywords"
                    type="text"
                    name="meta_keywords"
                    value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="ไทย, ธรรมะ, ท่องเที่ยว"
                >
                @error('meta_keywords')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-300">Meta คำอธิบาย</label>
                <textarea
                    id="meta_description"
                    name="meta_description"
                    rows="3"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                @error('meta_description')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="canonical_url" class="mb-1.5 block text-sm font-medium text-slate-300">Canonical URL</label>
                <input
                    id="canonical_url"
                    type="text"
                    name="canonical_url"
                    value="{{ old('canonical_url', $page->canonical_url ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('canonical_url')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                @php
                    $selectedOgImageId = (string) old('og_image_media_id', $page->og_image_media_id ?? '');
                @endphp

                <label class="mb-2 block text-sm font-medium text-slate-300">รูปภาพสำหรับแชร์</label>

                @if ($media->isEmpty())
                    <div class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-4 text-sm text-slate-400">
                        ยังไม่มีไฟล์รูปภาพ
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <label class="relative cursor-pointer overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:border-blue-400/40 hover:bg-white/[0.06]">
                            <input
                                type="radio"
                                name="og_image_media_id"
                                value=""
                                class="peer sr-only"
                                @checked($selectedOgImageId === '')
                            >

                            <div class="flex aspect-video items-center justify-center bg-slate-950/70 text-xs text-slate-500">
                                ไม่ใช้ OG Image
                            </div>

                            <div class="border-t border-white/10 p-3">
                                <p class="text-sm font-medium text-slate-200">ไม่ระบุรูป</p>
                                <p class="mt-0.5 text-xs text-slate-500">ใช้เริ่มต้นของระบบ</p>
                            </div>

                            <div class="pointer-events-none absolute inset-0 hidden rounded-2xl border-4 border-blue-300 bg-blue-500/10 ring-4 ring-blue-400/30 peer-checked:block"></div>
                            <div class="pointer-events-none absolute right-3 top-3 hidden rounded-full bg-blue-500 px-3 py-1 text-xs font-semibold text-white shadow-lg shadow-blue-950/40 peer-checked:block">
                                เลือกแล้ว
                            </div>
                        </label>

                        @foreach($media as $image)
                            @php
                                $imageUrl = $image->path
                                    ? (filter_var($image->path, FILTER_VALIDATE_URL)
                                        ? $image->path
                                        : \Illuminate\Support\Facades\Storage::url($image->path))
                                    : null;
                                $imageหัวข้อ = $image->title ?: $image->original_filename;
                            @endphp

                            <label class="relative cursor-pointer overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40 transition hover:border-blue-400/40 hover:bg-white/[0.06]">
                                <input
                                    type="radio"
                                    name="og_image_media_id"
                                    value="{{ $image->id }}"
                                    class="peer sr-only"
                                    @checked($selectedOgImageId === (string) $image->id)
                                >

                                <div class="aspect-video overflow-hidden bg-slate-950">
                                    @if ($imageUrl)
                                        <img
                                            src="{{ $imageUrl }}"
                                            alt="{{ $imageหัวข้อ }}"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs text-slate-500">
                                            ไม่มีตัวอย่างรูป
                                        </div>
                                    @endif
                                </div>

                                <div class="border-t border-white/10 p-3">
                                    <p class="truncate text-sm font-medium text-slate-200">
                                        {{ $imageหัวข้อ }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        #{{ $image->id }} · {{ $image->media_type }}
                                    </p>
                                </div>

                                <div class="pointer-events-none absolute inset-0 hidden rounded-2xl border-4 border-blue-300 bg-blue-500/10 ring-4 ring-blue-400/30 peer-checked:block"></div>
                                <div class="pointer-events-none absolute right-3 top-3 hidden rounded-full bg-blue-500 px-3 py-1 text-xs font-semibold text-white shadow-lg shadow-blue-950/40 peer-checked:block">
                                    เลือกแล้ว
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                @error('og_image_media_id')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="og_title" class="mb-1.5 block text-sm font-medium text-slate-300">OG Title</label>
                <input
                    id="og_title"
                    type="text"
                    name="og_title"
                    value="{{ old('og_title', $page->og_title ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('og_title')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="og_description" class="mb-1.5 block text-sm font-medium text-slate-300">OG คำอธิบาย</label>
                <textarea
                    id="og_description"
                    name="og_description"
                    rows="3"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >{{ old('og_description', $page->og_description ?? '') }}</textarea>
                @error('og_description')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Publishing Schedule --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <p class="text-sm font-medium text-blue-300">Publication</p>
            <h2 class="mt-1 text-lg font-semibold text-white">Publishing Schedule</h2>
            <p class="mt-1 text-sm text-slate-400">กำหนดช่วงเวลาที่หน้าเผยแพร่</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="published_at" class="mb-1.5 block text-sm font-medium text-slate-300">Published At</label>
                <input
                    id="published_at"
                    type="datetime-local"
                    name="published_at"
                    value="{{ old('published_at', isset($page) && $page->published_at ? $page->published_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('published_at')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="unpublished_at" class="mb-1.5 block text-sm font-medium text-slate-300">Unpublished At</label>
                <input
                    id="unpublished_at"
                    type="datetime-local"
                    name="unpublished_at"
                    value="{{ old('unpublished_at', isset($page) && $page->unpublished_at ? $page->unpublished_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('unpublished_at')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>
