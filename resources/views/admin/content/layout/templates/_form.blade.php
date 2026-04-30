<div class="space-y-6">
    {{-- Template Information --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                Template Setup
            </div>

            <h2 class="text-lg font-semibold text-white">Template Information</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อมูลหลักของ Template สำหรับ render หน้าเว็บไซต์</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Name <span class="text-rose-400">*</span>
                </label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $template->name ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น Default Page"
                    required
                >
                @error('name')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="key" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Key
                </label>
                <input
                    id="key"
                    type="text"
                    name="key"
                    value="{{ old('key', $template->key ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น default-page"
                >
                <p class="mt-1.5 text-xs text-slate-500">เว้นว่างได้ ระบบจะสร้างจากชื่อ Template ให้อัตโนมัติ</p>
                @error('key')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="view_path" class="mb-1.5 block text-sm font-medium text-slate-300">
                    View Path <span class="text-rose-400">*</span>
                </label>
                <input
                    id="view_path"
                    type="text"
                    name="view_path"
                    value="{{ old('view_path', $template->view_path ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น frontend.pages.default"
                    required
                >
                <p class="mt-1.5 text-xs text-slate-500">ใช้รูปแบบ Blade view path เช่น frontend.pages.default</p>
                @error('view_path')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Sort Order
                </label>
                <input
                    id="sort_order"
                    type="number"
                    name="sort_order"
                    min="0"
                    value="{{ old('sort_order', $template->sort_order ?? 0) }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                >
                @error('sort_order')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Status <span class="text-rose-400">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    required
                >
                    <option value="active" {{ old('status', $template->status ?? 'active') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="inactive" {{ old('status', $template->status ?? 'active') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                @error('status')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                <div class="flex items-start gap-3">
                    <input
                        id="is_default"
                        type="checkbox"
                        name="is_default"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20"
                        {{ old('is_default', $template->is_default ?? false) ? 'checked' : '' }}
                    >
                    <div>
                        <label for="is_default" class="text-sm font-medium text-white">
                            Set as default template
                        </label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">
                            ถ้าเลือก Template นี้ ค่า default เดิมจะถูกยกเลิก
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label for="description" class="mb-1.5 block text-sm font-medium text-slate-300">
                Description
            </label>
            <textarea
                id="description"
                name="description"
                rows="4"
                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                placeholder="คำอธิบาย Template เช่น ใช้สำหรับหน้า CMS ทั่วไป"
            >{{ old('description', $template->description ?? '') }}</textarea>
            @error('description')
                <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>