<div class="space-y-6">
    {{-- Section Information --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                Section Setup
            </div>

            <h2 class="text-lg font-semibold text-white">Section Information</h2>
            <p class="mt-1 text-sm text-slate-400">กำหนดข้อมูลหลักของ section และ component ที่ใช้แสดงผล</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Section Name <span class="text-rose-400">*</span>
                </label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $section->name ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น Hero Section"
                    required
                >
                @error('name')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="section_key" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Section Key <span class="text-rose-400">*</span>
                </label>
                <input
                    id="section_key"
                    type="text"
                    name="section_key"
                    value="{{ old('section_key', $section->section_key ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น homepage_hero"
                    required
                >
                @error('section_key')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="component_key" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Component Key <span class="text-rose-400">*</span>
                </label>
                <input
                    id="component_key"
                    type="text"
                    name="component_key"
                    value="{{ old('component_key', $section->component_key ?? '') }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder="เช่น hero, content_grid, temple_list"
                    required
                >
                @error('component_key')
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
                    value="{{ old('sort_order', $section->sort_order ?? 0) }}"
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
                    <option value="active" {{ old('status', $section->status ?? 'active') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="inactive" {{ old('status', $section->status ?? 'active') === 'inactive' ? 'selected' : '' }}>
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
                        id="is_visible"
                        type="checkbox"
                        name="is_visible"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-4 focus:ring-blue-500/20"
                        {{ old('is_visible', $section->is_visible ?? true) ? 'checked' : '' }}
                    >
                    <div>
                        <label for="is_visible" class="text-sm font-medium text-white">
                            Visible
                        </label>
                        <p class="mt-1 text-xs leading-5 text-slate-400">
                            ถ้าปิดไว้ section นี้จะไม่ถูกแสดงบนหน้าเว็บ
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Data --}}
    <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="mb-6 border-b border-white/10 pb-4">
            <div class="mb-3 inline-flex rounded-full border border-indigo-400/20 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-300">
                JSON Configuration
            </div>

            <h2 class="text-lg font-semibold text-white">Section Data</h2>
            <p class="mt-1 text-sm text-slate-400">ตั้งค่า settings และ content สำหรับ component ของ section นี้</p>
        </div>

        <div class="space-y-5">
            <div>
                <label for="settings" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Settings JSON
                </label>
                <textarea
                    id="settings"
                    name="settings"
                    rows="7"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 font-mono text-xs leading-6 text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder='{"background": "light", "layout": "centered"}'
                >{{ old('settings', isset($section) && $section->settings ? json_encode($section->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                @error('settings')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="content" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Content JSON
                </label>
                <textarea
                    id="content"
                    name="content"
                    rows="9"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 font-mono text-xs leading-6 text-white placeholder:text-slate-600 focus:border-blue-500/40 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    placeholder='{"title": "PAPAIWAT", "subtitle": "ค้นพบวัดและวัฒนธรรมไทย"}'
                >{{ old('content', isset($section) && $section->content ? json_encode($section->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                @error('content')
                    <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>