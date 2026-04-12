@php
    $folder = $folder ?? null;
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-6">
        <div>
            <label for="parent_id" class="mb-2 block text-sm font-medium text-slate-700">
                โฟลเดอร์แม่
            </label>
            <select
                id="parent_id"
                name="parent_id"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="">-- ไม่มี (Root Folder) --</option>
                @foreach ($parents as $parent)
                    <option
                        value="{{ $parent->id }}"
                        @selected(old('parent_id', $folder?->parent_id) == $parent->id)
                    >
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">
                ใช้จัดโครงสร้างโฟลเดอร์แบบลำดับชั้น
            </p>
            @error('parent_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                ชื่อโฟลเดอร์
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $folder?->name) }}"
                placeholder="เช่น Temple Gallery, Articles, Bangkok"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                required
            >
            <p class="mt-1 text-xs text-slate-500">
                ระบบจะใช้ชื่อนี้ในการสร้าง slug อัตโนมัติ
            </p>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

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
                ระบบจะสร้าง slug อัตโนมัติจากชื่อโฟลเดอร์
            </p>
        </div>

        <div>
            <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-700">
                ลำดับการแสดงผล
            </label>
            <input
                type="number"
                id="sort_order"
                name="sort_order"
                min="0"
                value="{{ old('sort_order', $folder?->sort_order ?? 0) }}"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            <p class="mt-1 text-xs text-slate-500">
                ค่าน้อยจะแสดงก่อน
            </p>
            @error('sort_order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">
                สถานะ
            </label>
            <select
                id="status"
                name="status"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="active" @selected(old('status', $folder?->status ?? 'active') === 'active')>
                    เปิดใช้งาน
                </option>
                <option value="inactive" @selected(old('status', $folder?->status) === 'inactive')>
                    ปิดใช้งาน
                </option>
            </select>
            <p class="mt-1 text-xs text-slate-500">
                โฟลเดอร์ที่ปิดใช้งานจะไม่ถูกใช้ใน flow หลักของระบบ
            </p>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="space-y-6">
        <div>
            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">
                คำอธิบาย
            </label>
            <textarea
                id="description"
                name="description"
                rows="8"
                placeholder="อธิบายการใช้งานของโฟลเดอร์นี้"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >{{ old('description', $folder?->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<script>
    const folderNameInput = document.getElementById('name');
    const slugPreviewInput = document.getElementById('slug_preview');

    function makeSlug(value) {
        return value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    function updateSlugPreview() {
        slugPreviewInput.value = makeSlug(folderNameInput.value);
    }

    folderNameInput.addEventListener('input', updateSlugPreview);
    updateSlugPreview();
</script>