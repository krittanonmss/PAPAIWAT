@php
    $category = $category ?? null;
    $selectedType = old('type_key', $category?->type_key);
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Left --}}
    <div class="space-y-6">
        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">ข้อมูลหลัก</h2>
            <p class="mt-1 text-sm text-slate-400">กำหนดชื่อ ประเภท และโครงสร้างของหมวดหมู่</p>

            <div class="mt-5 space-y-5">
                {{-- Parent --}}
                <div>
                    <label for="parent_id" class="mb-2 block text-sm font-medium text-slate-300">
                        หมวดหมู่แม่
                    </label>

                    <select
                        id="parent_id"
                        name="parent_id"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">-- ไม่มี (Root Category) --</option>
                        @foreach ($parents as $parent)
                            <option
                                value="{{ $parent->id }}"
                                data-type-key="{{ $parent->type_key }}"
                                @selected(old('parent_id', $category?->parent_id) == $parent->id)
                            >
                                {{ str_repeat('— ', (int) $parent->level) }}{{ $parent->name }} ({{ $parent->type_key }})
                            </option>
                        @endforeach
                    </select>

                    <p class="mt-1 text-xs text-slate-500">
                        ใช้จัดโครงสร้างหมวดหมู่แบบลำดับชั้น เช่น จังหวัด → วัด
                    </p>

                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-300">
                        ชื่อหมวดหมู่ <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $category?->name) }}"
                        placeholder="เช่น วัดในกรุงเทพ, ธรรมะฝึกจิต"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        required
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        ชื่อที่จะแสดงให้ผู้ใช้งานเห็น
                    </p>

                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug_preview" class="mb-2 block text-sm font-medium text-slate-300">
                        URL (Slug)
                    </label>

                    <input
                        type="text"
                        id="slug_preview"
                        class="w-full rounded-xl border border-white/10 bg-slate-800/80 px-3 py-2.5 text-sm text-slate-400 outline-none"
                        readonly
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        ระบบจะแสดงตัวอย่าง URL จากชื่อหมวดหมู่
                    </p>
                </div>

                {{-- Type --}}
                <div>
                    <label for="type_key" class="mb-2 block text-sm font-medium text-slate-300">
                        ประเภทหมวดหมู่ <span class="text-red-400">*</span>
                    </label>

                    <select
                        id="type_key"
                        name="type_key"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        required
                    >
                        <option value="">-- เลือกประเภท --</option>
                        @foreach ($types as $type)
                            <option
                                value="{{ $type }}"
                                @selected($selectedType === $type)
                            >
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>

                    @error('type_key')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">การแสดงผล</h2>
            <p class="mt-1 text-sm text-slate-400">ควบคุมลำดับ สถานะ และการแนะนำหมวดหมู่</p>

            <div class="mt-5 space-y-5">
                {{-- Sort --}}
                <div>
                    <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-300">
                        ลำดับการแสดงผล
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', $category?->sort_order ?? 0) }}"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-slate-300">
                        สถานะ
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="active" @selected(old('status', $category?->status ?? 'active') === 'active')>
                            เปิดใช้งาน
                        </option>
                        <option value="inactive" @selected(old('status', $category?->status) === 'inactive')>
                            ปิดใช้งาน
                        </option>
                    </select>
                </div>

                {{-- Featured --}}
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-slate-900/70 p-4 hover:bg-slate-900">
                    <input type="hidden" name="is_featured" value="0">

                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        @checked(old('is_featured', $category?->is_featured))
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-900 text-blue-500 focus:ring-blue-500/30"
                    >

                    <span>
                        <span class="block text-sm font-medium text-slate-200">
                            หมวดหมู่แนะนำ
                        </span>
                        <span class="mt-1 block text-xs text-slate-500">
                            ใช้สำหรับ highlight หรือแสดงในพื้นที่แนะนำของเว็บไซต์
                        </span>
                    </span>
                </label>
            </div>
        </div>
    </div>

    {{-- Right --}}
    <div class="space-y-6">
        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">รายละเอียด</h2>
            <p class="mt-1 text-sm text-slate-400">คำอธิบายสำหรับผู้ดูแลและหน้าเว็บไซต์</p>

            <div class="mt-5">
                <label for="description" class="mb-2 block text-sm font-medium text-slate-300">
                    คำอธิบาย
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="7"
                    placeholder="อธิบายรายละเอียดของหมวดหมู่นี้"
                    class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >{{ old('description', $category?->description) }}</textarea>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">SEO</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อมูลสำหรับการแสดงผลบน Search Engine</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="meta_title" class="mb-2 block text-sm font-medium text-slate-300">
                        Meta Title
                    </label>

                    <input
                        type="text"
                        id="meta_title"
                        name="meta_title"
                        value="{{ old('meta_title', $category?->meta_title) }}"
                        placeholder="หัวข้อสำหรับ SEO"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div>
                    <label for="meta_description" class="mb-2 block text-sm font-medium text-slate-300">
                        Meta Description
                    </label>

                    <textarea
                        id="meta_description"
                        name="meta_description"
                        rows="6"
                        placeholder="คำอธิบายสั้นสำหรับ SEO"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >{{ old('meta_description', $category?->meta_description) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const nameInput = document.getElementById('name');
        const slugPreview = document.getElementById('slug_preview');
        const typeSelect = document.getElementById('type_key');
        const parentSelect = document.getElementById('parent_id');

        if (!nameInput || !slugPreview || !typeSelect || !parentSelect) {
            return;
        }

        const makeSlug = (value) => {
            const slug = value
                .toString()
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');

            return slug || 'category-auto-generated';
        };

        const updateSlug = () => {
            slugPreview.value = makeSlug(nameInput.value);
        };

        const updateParentOptions = () => {
            const selectedType = typeSelect.value;

            Array.from(parentSelect.options).forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden = Boolean(selectedType) && option.dataset.typeKey !== selectedType;
            });

            const selectedOption = parentSelect.selectedOptions[0];

            if (selectedOption?.hidden) {
                parentSelect.value = '';
                parentSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };

        nameInput.addEventListener('input', updateSlug);
        typeSelect.addEventListener('change', updateParentOptions);

        updateSlug();
        updateParentOptions();
    })();
</script>
