@php
    $isEdit = isset($menu);
@endphp

<div class="space-y-6">

    {{-- Section: Main Information --}}
    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
        <h3 class="text-sm font-semibold text-white">ข้อมูลเมนู</h3>

        <div class="mt-4 grid gap-6 lg:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ชื่อเมนู <span class="text-red-300">*</span>
                </label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $menu->name ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    placeholder="เช่น Header Menu"
                    required
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Slug
                </label>
                <input
                    id="slug"
                    type="text"
                    name="slug"
                    value="{{ old('slug', $menu->slug ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    placeholder="เช่น header-menu"
                >
                <p class="mt-1 text-xs text-slate-500">
                    เว้นว่างได้ ระบบจะสร้างจากชื่อเมนูให้อัตโนมัติ
                </p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Section: Usage --}}
    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
        <h3 class="text-sm font-semibold text-white">ตำแหน่งการใช้งาน</h3>

        <div class="mt-4 grid gap-6 lg:grid-cols-2">
            <div>
                <label for="location_key" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ตำแหน่งเมนู
                </label>
                <select
                    id="location_key"
                    name="location_key"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >
                    <option value="" class="bg-slate-900">ไม่ระบุ</option>
                    <option value="header" class="bg-slate-900" @selected(old('location_key', $menu->location_key ?? '') === 'header')>
                        Header
                    </option>
                    <option value="footer" class="bg-slate-900" @selected(old('location_key', $menu->location_key ?? '') === 'footer')>
                        Footer
                    </option>
                    <option value="sidebar" class="bg-slate-900" @selected(old('location_key', $menu->location_key ?? '') === 'sidebar')>
                        Sidebar
                    </option>
                </select>
                <p class="mt-1 text-xs text-slate-500">
                    เลือกตำแหน่งที่เมนูนี้จะถูกนำไปแสดงผล
                </p>
                @error('location_key')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                    สถานะ <span class="text-red-300">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    required
                >
                    <option value="active" class="bg-slate-900" @selected(old('status', $menu->status ?? 'active') === 'active')>
                        เปิดใช้งาน
                    </option>
                    <option value="inactive" class="bg-slate-900" @selected(old('status', $menu->status ?? 'active') === 'inactive')>
                        ปิดใช้งาน
                    </option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Section: Advanced Settings --}}
    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
        <h3 class="text-sm font-semibold text-white">ตั้งค่าเพิ่มเติม</h3>

        <div class="mt-4 grid gap-6 lg:grid-cols-2">
            <div>
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">
                    ลำดับการแสดงผล
                </label>
                <input
                    id="sort_order"
                    type="number"
                    name="sort_order"
                    min="0"
                    value="{{ old('sort_order', $menu->sort_order ?? 0) }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                >
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center rounded-2xl border border-yellow-400/20 bg-yellow-500/10 px-4 py-3">
                <input
                    id="is_default"
                    type="checkbox"
                    name="is_default"
                    value="1"
                    class="h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-blue-500"
                    {{ old('is_default', $menu->is_default ?? false) ? 'checked' : '' }}
                >
                <div class="ml-3">
                    <label for="is_default" class="text-sm font-medium text-yellow-200">
                        ตั้งเป็นเมนูหลัก (Default)
                    </label>
                    <p class="text-xs text-yellow-300/70">
                        ถ้าเลือกเมนูนี้ เมนู default เดิมจะถูกยกเลิกอัตโนมัติ
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Description --}}
    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
        <h3 class="text-sm font-semibold text-white">คำอธิบาย</h3>

        <div class="mt-4">
            <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">
                รายละเอียดเมนู
            </label>
            <textarea
                id="description"
                name="description"
                rows="4"
                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                placeholder="คำอธิบายเมนู เช่น ใช้สำหรับเมนูหลักด้านบนของเว็บไซต์"
            >{{ old('description', $menu->description ?? '') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>