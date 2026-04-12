@php
    $category = $category ?? null;
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-6">

        {{-- Parent --}}
        <div>
            <label for="parent_id" class="mb-2 block text-sm font-medium text-slate-700">
                หมวดหมู่แม่
            </label>

            <select
                id="parent_id"
                name="parent_id"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="">-- ไม่มี (Root Category) --</option>
                @foreach ($parents as $parent)
                    <option
                        value="{{ $parent->id }}"
                        @selected(old('parent_id', $category?->parent_id) == $parent->id)
                    >
                        {{ $parent->name }} ({{ $parent->type_key }})
                    </option>
                @endforeach
            </select>

            <p class="mt-1 text-xs text-slate-500">
                ใช้จัดโครงสร้างหมวดหมู่แบบลำดับชั้น เช่น จังหวัด → วัด
            </p>

            @error('parent_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Name --}}
        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                ชื่อหมวดหมู่
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $category?->name) }}"
                placeholder="เช่น วัดในกรุงเทพ, ธรรมะฝึกจิต"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                required
            >

            <p class="mt-1 text-xs text-slate-500">
                ชื่อที่จะแสดงให้ผู้ใช้งานเห็น
            </p>

            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Slug Preview --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
                URL (Slug)
            </label>

            <input
                type="text"
                id="slug_preview"
                class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 focus:outline-none"
                readonly
            >

            <p class="mt-1 text-xs text-slate-500">
                ระบบจะสร้าง URL อัตโนมัติจากชื่อหมวดหมู่ เช่น <code>wat-bangkok</code>
            </p>
        </div>

        {{-- Type --}}
        <div>
            <label for="type_key" class="mb-2 block text-sm font-medium text-slate-700">
                ประเภทหมวดหมู่
            </label>

            <select
                id="type_key"
                name="type_key"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                required
            >
                <option value="">-- เลือกประเภท --</option>
                @foreach ($types as $type)
                    <option
                        value="{{ $type }}"
                        @selected(old('type_key', $category?->type_key) === $type)
                    >
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>

            <p class="mt-1 text-xs text-slate-500">
                ใช้กำหนดว่าหมวดหมู่นี้ใช้กับเนื้อหาประเภทอะไร เช่น วัด หรือ บทความ
            </p>

            @error('type_key')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Sort --}}
        <div>
            <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-700">
                ลำดับการแสดงผล
            </label>

            <input
                type="number"
                id="sort_order"
                name="sort_order"
                min="0"
                value="{{ old('sort_order', $category?->sort_order ?? 0) }}"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >

            <p class="mt-1 text-xs text-slate-500">
                ค่าน้อยจะแสดงก่อน (ใช้จัดเรียงหมวดหมู่)
            </p>

            @error('sort_order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status --}}
        <div>
            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">
                สถานะ
            </label>

            <select
                id="status"
                name="status"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="active" @selected(old('status', $category?->status ?? 'active') === 'active')>
                    เปิดใช้งาน
                </option>
                <option value="inactive" @selected(old('status', $category?->status) === 'inactive')>
                    ปิดใช้งาน
                </option>
            </select>

            <p class="mt-1 text-xs text-slate-500">
                หมวดหมู่ที่ปิดใช้งานจะไม่ถูกนำไปแสดงในระบบ
            </p>

            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Featured --}}
        <div class="flex items-start gap-2">
            <input type="hidden" name="is_featured" value="0">

            <input
                type="checkbox"
                id="is_featured"
                name="is_featured"
                value="1"
                @checked(old('is_featured', $category?->is_featured))
                class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500"
            >

            <div>
                <label for="is_featured" class="text-sm font-medium text-slate-700">
                    หมวดหมู่แนะนำ
                </label>
                <p class="text-xs text-slate-500">
                    ใช้สำหรับ highlight หรือแสดงในตำแหน่งพิเศษ
                </p>
            </div>
        </div>
    </div>

    {{-- Right --}}
    <div class="space-y-6">

        <div>
            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">
                คำอธิบาย
            </label>

            <textarea
                id="description"
                name="description"
                rows="5"
                placeholder="อธิบายเกี่ยวกับหมวดหมู่นี้ (ไม่บังคับ)"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >{{ old('description', $category?->description) }}</textarea>

            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="meta_title" class="mb-2 block text-sm font-medium text-slate-700">
                Meta Title
            </label>

            <input
                type="text"
                id="meta_title"
                name="meta_title"
                placeholder="สำหรับ SEO (ไม่บังคับ)"
                value="{{ old('meta_title', $category?->meta_title) }}"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >

            @error('meta_title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="meta_description" class="mb-2 block text-sm font-medium text-slate-700">
                Meta Description
            </label>

            <textarea
                id="meta_description"
                name="meta_description"
                rows="5"
                placeholder="คำอธิบายสำหรับ SEO (ไม่บังคับ)"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >{{ old('meta_description', $category?->meta_description) }}</textarea>

            @error('meta_description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<script>
    const nameInput = document.getElementById('name');
    const slugPreview = document.getElementById('slug_preview');

    function makeSlug(value) {
        return value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    function updateSlug() {
        slugPreview.value = makeSlug(nameInput.value);
    }

    nameInput.addEventListener('input', updateSlug);
    updateSlug();
</script>