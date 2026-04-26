<div class="space-y-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-slate-900">Template Information</h2>
            <p class="mt-1 text-sm text-slate-500">ข้อมูลหลักของ Template สำหรับ render หน้าเว็บไซต์</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Name <span class="text-red-500">*</span>
                </label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $template->name ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="เช่น Default Page"
                    required
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="key" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Key
                </label>
                <input
                    id="key"
                    type="text"
                    name="key"
                    value="{{ old('key', $template->key ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="เช่น default-page"
                >
                <p class="mt-1 text-xs text-slate-500">เว้นว่างได้ ระบบจะสร้างจากชื่อ Template ให้อัตโนมัติ</p>
                @error('key')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="view_path" class="mb-1.5 block text-sm font-medium text-slate-700">
                    View Path <span class="text-red-500">*</span>
                </label>
                <input
                    id="view_path"
                    type="text"
                    name="view_path"
                    value="{{ old('view_path', $template->view_path ?? '') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                    placeholder="เช่น frontend.pages.default"
                    required
                >
                <p class="mt-1 text-xs text-slate-500">ใช้รูปแบบ Blade view path เช่น frontend.pages.default</p>
                @error('view_path')
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
                    value="{{ old('sort_order', $template->sort_order ?? 0) }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
                @error('sort_order')
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
                    <option value="active" {{ old('status', $template->status ?? 'active') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="inactive" {{ old('status', $template->status ?? 'active') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <input
                    id="is_default"
                    type="checkbox"
                    name="is_default"
                    value="1"
                    class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                    {{ old('is_default', $template->is_default ?? false) ? 'checked' : '' }}
                >
                <div class="ml-3">
                    <label for="is_default" class="text-sm font-medium text-slate-800">
                        Set as default template
                    </label>
                    <p class="text-xs text-slate-500">ถ้าเลือก Template นี้ ค่า default เดิมจะถูกยกเลิก</p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">
                Description
            </label>
            <textarea
                id="description"
                name="description"
                rows="4"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                placeholder="คำอธิบาย Template เช่น ใช้สำหรับหน้า CMS ทั่วไป"
            >{{ old('description', $template->description ?? '') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>