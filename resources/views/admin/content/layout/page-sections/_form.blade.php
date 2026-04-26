<div class="space-y-6">
    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">
                Section Name <span class="text-red-500">*</span>
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $section->name ?? '') }}"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                placeholder="เช่น Hero Section"
                required
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="section_key" class="mb-1.5 block text-sm font-medium text-slate-700">
                Section Key <span class="text-red-500">*</span>
            </label>
            <input
                id="section_key"
                type="text"
                name="section_key"
                value="{{ old('section_key', $section->section_key ?? '') }}"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                placeholder="เช่น homepage_hero"
                required
            >
            @error('section_key')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="component_key" class="mb-1.5 block text-sm font-medium text-slate-700">
                Component Key <span class="text-red-500">*</span>
            </label>
            <input
                id="component_key"
                type="text"
                name="component_key"
                value="{{ old('component_key', $section->component_key ?? '') }}"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                placeholder="เช่น hero, content_grid, temple_list"
                required
            >
            @error('component_key')
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
                value="{{ old('sort_order', $section->sort_order ?? 0) }}"
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
                <option value="active" {{ old('status', $section->status ?? 'active') === 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="inactive" {{ old('status', $section->status ?? 'active') === 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input
                id="is_visible"
                type="checkbox"
                name="is_visible"
                value="1"
                class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                {{ old('is_visible', $section->is_visible ?? true) ? 'checked' : '' }}
            >
            <div class="ml-3">
                <label for="is_visible" class="text-sm font-medium text-slate-800">
                    Visible
                </label>
                <p class="text-xs text-slate-500">ถ้าปิดไว้ section นี้จะไม่ถูกแสดงบนหน้าเว็บ</p>
            </div>
        </div>
    </div>

    <div>
        <label for="settings" class="mb-1.5 block text-sm font-medium text-slate-700">
            Settings JSON
        </label>
        <textarea
            id="settings"
            name="settings"
            rows="7"
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 font-mono text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
            placeholder='{"background": "light", "layout": "centered"}'
        >{{ old('settings', isset($section) && $section->settings ? json_encode($section->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        @error('settings')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="content" class="mb-1.5 block text-sm font-medium text-slate-700">
            Content JSON
        </label>
        <textarea
            id="content"
            name="content"
            rows="9"
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 font-mono text-xs text-slate-900 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
            placeholder='{"title": "PAPAIWAT", "subtitle": "ค้นพบวัดและวัฒนธรรมไทย"}'
        >{{ old('content', isset($section) && $section->content ? json_encode($section->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        @error('content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>