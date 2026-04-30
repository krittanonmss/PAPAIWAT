@php
    /** @var \App\Models\Content\Article\ArticleTag|null $articleTag */
@endphp

<section class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
    <div class="border-b border-white/10 px-6 py-4">
        <h2 class="text-base font-semibold text-white">ข้อมูลแท็กบทความ</h2>
        <p class="mt-1 text-xs text-slate-400">
            กรอกชื่อแท็ก slug สถานะ และลำดับการแสดงผล
        </p>
    </div>

    <div class="grid gap-6 p-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">
                ชื่อแท็ก <span class="text-rose-400">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $articleTag->name ?? '') }}"
                class="@error('name') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                placeholder="กรอกชื่อแท็ก เช่น ประวัติศาสตร์, วัฒนธรรม, ท่องเที่ยว"
            >
            @error('name')
                <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-300">
                Slug
            </label>
            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ old('slug', $articleTag->slug ?? '') }}"
                class="@error('slug') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                placeholder="เว้นว่างไว้เพื่อให้ระบบสร้างอัตโนมัติ"
            >
            <p class="mt-1.5 text-xs text-slate-500">
                ใช้เป็น key สำหรับ URL หรือการอ้างอิงแท็ก ควรเป็นตัวอักษรภาษาอังกฤษ ตัวเลข และขีดกลาง
            </p>
            @error('slug')
                <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">
                ลำดับการแสดงผล
            </label>
            <input
                type="number"
                id="sort_order"
                name="sort_order"
                value="{{ old('sort_order', $articleTag->sort_order ?? 0) }}"
                min="0"
                class="@error('sort_order') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
            >
            <p class="mt-1.5 text-xs text-slate-500">
                ค่าน้อยจะแสดงก่อน
            </p>
            @error('sort_order')
                <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                สถานะ <span class="text-rose-400">*</span>
            </label>
            <select
                id="status"
                name="status"
                class="@error('status') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
            >
                <option value="active" @selected(old('status', $articleTag->status ?? 'active') === 'active')>เปิดใช้งาน</option>
                <option value="inactive" @selected(old('status', $articleTag->status ?? 'active') === 'inactive')>ปิดใช้งาน</option>
            </select>
            @error('status')
                <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>
    </div>
</section>