<div class="space-y-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-slate-900">Page Information</h2>
            <p class="mt-1 text-sm text-slate-500">ข้อมูลหลักของหน้าเว็บไซต์</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Title <span class="text-red-500">*</span>
                </label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $page->title ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="เช่น หน้าแรก"
                    required
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Slug
                </label>
                <input
                    id="slug"
                    type="text"
                    name="slug"
                    value="{{ old('slug', $page->slug ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="เช่น home"
                >
                <p class="mt-1 text-xs text-slate-500">เว้นว่างได้ ระบบจะสร้างจากชื่อหน้าให้อัตโนมัติ</p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="template_id" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Template
                </label>
                <select
                    id="template_id"
                    name="template_id"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                    <option value="">-- No Template --</option>
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
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="page_type" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Page Type <span class="text-red-500">*</span>
                </label>
                <input
                    id="page_type"
                    type="text"
                    name="page_type"
                    value="{{ old('page_type', $page->page_type ?? 'custom') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="custom"
                    required
                >
                @error('page_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    required
                >
                    @foreach(['draft', 'published', 'archived'] as $status)
                        <option
                            value="{{ $status }}"
                            {{ old('status', $page->status ?? 'draft') === $status ? 'selected' : '' }}
                        >
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Sort Order
                </label>
                <input
                    id="sort_order"
                    type="number"
                    name="sort_order"
                    min="0"
                    value="{{ old('sort_order', $page->sort_order ?? 0) }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 lg:col-span-2">
                <input
                    id="is_homepage"
                    type="checkbox"
                    name="is_homepage"
                    value="1"
                    class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                    {{ old('is_homepage', $page->is_homepage ?? false) ? 'checked' : '' }}
                >
                <div class="ml-3">
                    <label for="is_homepage" class="text-sm font-medium text-slate-800">
                        Set as homepage
                    </label>
                    <p class="text-xs text-slate-500">ถ้าเลือกหน้านี้ หน้า homepage เดิมจะถูกยกเลิก</p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-slate-900">Content Summary</h2>
            <p class="mt-1 text-sm text-slate-500">ข้อความสรุปและรายละเอียดของหน้า</p>
        </div>

        <div class="space-y-6">
            <div>
                <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-700">Excerpt</label>
                <input
                    id="excerpt"
                    type="text"
                    name="excerpt"
                    value="{{ old('excerpt', $page->excerpt ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="คำอธิบายสั้น ๆ ของหน้า"
                >
                @error('excerpt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="รายละเอียดเพิ่มเติมของหน้า"
                >{{ old('description', $page->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-slate-900">SEO Settings</h2>
            <p class="mt-1 text-sm text-slate-500">ข้อมูลสำหรับ search engine และ social sharing</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-700">Meta Title</label>
                <input
                    id="meta_title"
                    type="text"
                    name="meta_title"
                    value="{{ old('meta_title', $page->meta_title ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                @error('meta_title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="meta_keywords" class="mb-1.5 block text-sm font-medium text-slate-700">Meta Keywords</label>
                <input
                    id="meta_keywords"
                    type="text"
                    name="meta_keywords"
                    value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="วัดไทย, ธรรมะ, ท่องเที่ยว"
                >
                @error('meta_keywords')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-700">Meta Description</label>
                <textarea
                    id="meta_description"
                    name="meta_description"
                    rows="3"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                @error('meta_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="canonical_url" class="mb-1.5 block text-sm font-medium text-slate-700">Canonical URL</label>
                <input
                    id="canonical_url"
                    type="text"
                    name="canonical_url"
                    value="{{ old('canonical_url', $page->canonical_url ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                @error('canonical_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="og_image_media_id" class="mb-1.5 block text-sm font-medium text-slate-700">OG Image</label>
                <select
                    id="og_image_media_id"
                    name="og_image_media_id"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                    <option value="">-- No Image --</option>
                    @foreach($media as $image)
                        <option
                            value="{{ $image->id }}"
                            {{ old('og_image_media_id', $page->og_image_media_id ?? '') == $image->id ? 'selected' : '' }}
                        >
                            {{ $image->title ?: $image->original_filename }}
                        </option>
                    @endforeach
                </select>
                @error('og_image_media_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="og_title" class="mb-1.5 block text-sm font-medium text-slate-700">OG Title</label>
                <input
                    id="og_title"
                    type="text"
                    name="og_title"
                    value="{{ old('og_title', $page->og_title ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                @error('og_title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="og_description" class="mb-1.5 block text-sm font-medium text-slate-700">OG Description</label>
                <textarea
                    id="og_description"
                    name="og_description"
                    rows="3"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >{{ old('og_description', $page->og_description ?? '') }}</textarea>
                @error('og_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-slate-900">Publishing Schedule</h2>
            <p class="mt-1 text-sm text-slate-500">กำหนดช่วงเวลาที่หน้าเผยแพร่</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label for="published_at" class="mb-1.5 block text-sm font-medium text-slate-700">Published At</label>
                <input
                    id="published_at"
                    type="datetime-local"
                    name="published_at"
                    value="{{ old('published_at', isset($page) && $page->published_at ? $page->published_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                @error('published_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="unpublished_at" class="mb-1.5 block text-sm font-medium text-slate-700">Unpublished At</label>
                <input
                    id="unpublished_at"
                    type="datetime-local"
                    name="unpublished_at"
                    value="{{ old('unpublished_at', isset($page) && $page->unpublished_at ? $page->unpublished_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                @error('unpublished_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>