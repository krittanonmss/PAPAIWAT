@php
    $folder = $folder ?? null;
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-6">
        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">ข้อมูลโฟลเดอร์</h2>
            <p class="mt-1 text-sm text-slate-400">กำหนดชื่อและโครงสร้างของโฟลเดอร์</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="parent_id" class="mb-2 block text-sm font-medium text-slate-300">
                        โฟลเดอร์แม่
                    </label>

                    <select
                        id="parent_id"
                        name="parent_id"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
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
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-300">
                        ชื่อโฟลเดอร์ <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $folder?->name) }}"
                        placeholder="เช่น Temple Gallery, Articles, Bangkok"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        required
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        ระบบจะใช้ชื่อนี้ในการสร้าง slug อัตโนมัติ
                    </p>

                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

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
                        ระบบจะแสดงตัวอย่าง slug จากชื่อโฟลเดอร์
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">การแสดงผล</h2>
            <p class="mt-1 text-sm text-slate-400">ควบคุมลำดับและสถานะของโฟลเดอร์</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-300">
                        ลำดับการแสดงผล
                    </label>

                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        min="0"
                        value="{{ old('sort_order', $folder?->sort_order ?? 0) }}"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        น้อยจะแสดงก่อน
                    </p>

                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-slate-300">
                        สถานะ
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
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
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">รายละเอียด</h2>
            <p class="mt-1 text-sm text-slate-400">คำอธิบายสำหรับช่วยแยกประเภทการใช้งานของโฟลเดอร์</p>

            <div class="mt-5">
                <label for="description" class="mb-2 block text-sm font-medium text-slate-300">
                    คำอธิบาย
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="8"
                    placeholder="อธิบายการใช้งานของโฟลเดอร์นี้"
                    class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >{{ old('description', $folder?->description) }}</textarea>

                @error('description')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<script>
    const folderชื่อInput = document.getElementById('name');
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
        slugPreviewInput.value = makeSlug(folderชื่อInput.value);
    }

    folderชื่อInput.addEventListener('input', updateSlugPreview);
    updateSlugPreview();
</script>