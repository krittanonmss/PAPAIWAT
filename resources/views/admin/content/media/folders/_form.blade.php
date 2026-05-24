@php
    $folder = $folder ?? null;
@endphp

<div class="space-y-6">
    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ข้อมูลโฟลเดอร์</h2>
            <p class="mt-1 text-xs text-slate-400">กำหนดชื่อ ตำแหน่ง และ slug สำหรับจัดกลุ่มไฟล์สื่อ</p>
        </div>

        <div class="grid gap-5 p-6 xl:grid-cols-[minmax(0,1fr)_minmax(280px,0.72fr)]">
            <div class="space-y-5">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">
                        ชื่อโฟลเดอร์ <span class="text-rose-400">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $folder?->name) }}"
                        placeholder="เช่น Temple Gallery, Articles, Bangkok"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        required
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        ระบบจะใช้ชื่อนี้ในการสร้าง slug อัตโนมัติ
                    </p>

                    @error('name')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="parent_id" class="mb-1.5 block text-sm font-medium text-slate-300">
                        โฟลเดอร์แม่
                    </label>

                    <select
                        id="parent_id"
                        name="parent_id"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ไม่มี (Root Folder)</option>
                        @foreach ($parents as $parent)
                            <option
                                value="{{ $parent->id }}"
                                class="bg-slate-900"
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
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                <div>
                    <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-300">
                        URL (Slug)
                    </label>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $folder?->slug) }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="เว้นว่างไว้เพื่อให้ระบบสร้างอัตโนมัติ"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        กรอกเองได้ หรือเว้นว่างไว้เพื่อให้ระบบสร้างจากชื่อโฟลเดอร์ ถ้าชื่อเป็นภาษาไทยระบบจะแปลงเป็นตัวอักษรอังกฤษให้
                    </p>
                    @error('slug')
                        <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4 rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                    <p class="text-sm font-medium text-blue-100">ตำแหน่งในคลังสื่อ</p>
                    <p class="mt-1 text-xs leading-5 text-blue-200/80">
                        ไม่เลือกโฟลเดอร์แม่หากต้องการให้แสดงเป็น root folder ใน sidebar ของคลังสื่อ
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">การแสดงผล</h2>
            <p class="mt-1 text-xs text-slate-400">ควบคุมลำดับและสถานะของโฟลเดอร์</p>
        </div>

        <div class="grid gap-5 p-6 lg:grid-cols-[220px_minmax(220px,0.8fr)_minmax(0,1fr)]">
            <div>
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ลำดับการแสดงผล
                </label>

                <input
                    type="number"
                    id="sort_order"
                    name="sort_order"
                    min="0"
                    value="{{ old('sort_order', $folder?->sort_order ?? 0) }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >

                <p class="mt-1 text-xs text-slate-500">
                    น้อยจะแสดงก่อน
                </p>

                @error('sort_order')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                    สถานะ
                </label>

                <select
                    id="status"
                    name="status"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >
                    <option value="active" class="bg-slate-900" @selected(old('status', $folder?->status ?? 'active') === 'active')>
                        เปิดใช้งาน
                    </option>
                    <option value="inactive" class="bg-slate-900" @selected(old('status', $folder?->status) === 'inactive')>
                        ปิดใช้งาน
                    </option>
                </select>

                <p class="mt-1 text-xs text-slate-500">
                    โฟลเดอร์ที่ปิดใช้งานจะไม่ถูกใช้ใน flow หลักของระบบ
                </p>

                @error('status')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                <p class="text-sm font-medium text-slate-200">ผลต่อการเรียงรายการ</p>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    ระบบเรียงตามลำดับก่อน แล้วจึงเรียงตามชื่อโฟลเดอร์เมื่อมีลำดับเท่ากัน
                </p>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">รายละเอียด</h2>
            <p class="mt-1 text-xs text-slate-400">คำอธิบายสำหรับช่วยแยกประเภทการใช้งานของโฟลเดอร์</p>
        </div>

        <div class="grid gap-5 p-6 xl:grid-cols-[minmax(0,1fr)_280px]">
            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">
                    คำอธิบาย
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="อธิบายการใช้งานของโฟลเดอร์นี้"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >{{ old('description', $folder?->description) }}</textarea>

                @error('description')
                    <p class="mt-1 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                <p class="text-sm font-medium text-slate-200">คำอธิบายภายในระบบ</p>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    ใช้ช่วยทีมแยกกลุ่มไฟล์ ไม่แสดงเป็นเนื้อหาหลักบนหน้าเว็บไซต์
                </p>
            </div>
        </div>
    </section>
</div>

<script>
    (() => {
        const folderNameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        let slugEdited = Boolean(slugInput?.value);

        if (!folderNameInput || !slugInput) {
            return;
        }

        function makeSlug(value) {
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
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        function updateSlugPreview() {
            if (!slugEdited) {
                slugInput.value = makeSlug(folderNameInput.value);
            }
        }

        folderNameInput.addEventListener('input', updateSlugPreview);
        slugInput.addEventListener('input', () => {
            slugEdited = true;
        });
        updateSlugPreview();
    })();
</script>
