<x-layouts.admin title="การตั้งค่าส่วนตัว" header="การตั้งค่าส่วนตัว">
    @php
        $preferenceErrors = $errors->getBag('preferences');
        $pref = fn (string $key) => old($key, $preferences[$key] ?? null);
    @endphp

    <div class="space-y-5 text-white">
        <div class="px-6 py-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-300">ตั้งค่าการใช้งาน</p>
                    <h1 class="mt-2 text-2xl font-bold text-white">การตั้งค่าส่วนตัว</h1>
                    <p class="mt-1 text-sm text-slate-300">ค่าเริ่มต้นและพฤติกรรมการใช้งานหลังบ้านของบัญชี {{ $admin?->username ?? '-' }}</p>
                </div>

                <a href="{{ route('admin.profile.edit') }}" class="inline-flex w-fit items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    โปรไฟล์ของฉัน
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($preferenceErrors->any())
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-3 text-sm text-red-300">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($preferenceErrors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="POST" action="{{ route('admin.preferences.update') }}" class="space-y-6" data-admin-autosave="off">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="display_theme" class="mb-1.5 block text-sm font-medium text-slate-400">ธีมหน้าจอ</label>
                        <select id="display_theme" name="display[theme]" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                            <option value="dark" @selected($pref('display.theme') === 'dark')>มืด</option>
                            <option value="light" @selected($pref('display.theme') === 'light')>สว่าง</option>
                            <option value="system" @selected($pref('display.theme') === 'system')>ตามระบบ</option>
                        </select>
                    </div>

                    <div>
                        <label for="display_density" class="mb-1.5 block text-sm font-medium text-slate-400">ความแน่นของหน้าจอ</label>
                        <select id="display_density" name="display[density]" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                            <option value="comfortable" @selected($pref('display.density') === 'comfortable')>สบายตา</option>
                            <option value="compact" @selected($pref('display.density') === 'compact')>กระชับ</option>
                        </select>
                    </div>

                    <div>
                        <label for="display_scale" class="mb-1.5 block text-sm font-medium text-slate-400">ขนาดหน้าจอหลังบ้าน</label>
                        <input id="display_scale" type="number" name="display[scale]" min="70" max="100" step="5" value="{{ $pref('display.scale') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div>
                        <label for="tables_default_per_page" class="mb-1.5 block text-sm font-medium text-slate-400">จำนวนรายการต่อหน้า</label>
                        <select id="tables_default_per_page" name="tables[default_per_page]" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                            @foreach (\App\Services\Admin\AdminPreferenceService::PER_PAGE_OPTIONS as $perPage)
                                <option value="{{ $perPage }}" @selected((int) $pref('tables.default_per_page') === $perPage)>{{ $perPage }} รายการ</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="media_default_view_mode" class="mb-1.5 block text-sm font-medium text-slate-400">รูปแบบการแสดงคลังสื่อ</label>
                        <select id="media_default_view_mode" name="media[default_view_mode]" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                            <option value="grid" @selected($pref('media.default_view_mode') === 'grid')>ตารางรูปภาพ</option>
                            <option value="list" @selected($pref('media.default_view_mode') === 'list')>รายการ</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ([
                        ['display[sidebar_collapsed]', 'display.sidebar_collapsed', 'เริ่มต้นด้วยเมนูด้านข้างแบบย่อ'],
                        ['tables[remember_filters]', 'tables.remember_filters', 'จำ filter ล่าสุด'],
                        ['tables[open_detail_in_new_tab]', 'tables.open_detail_in_new_tab', 'เปิดหน้ารายละเอียดในแท็บใหม่'],
                        ['editor[autosave_drafts]', 'editor.autosave_drafts', 'บันทึกร่างอัตโนมัติใน editor'],
                        ['editor[preview_panel_open]', 'editor.preview_panel_open', 'เปิดแผง preview เป็นค่าเริ่มต้น'],
                        ['notifications[in_app]', 'notifications.in_app', 'แจ้งเตือนในระบบ'],
                        ['notifications[email]', 'notifications.email', 'แจ้งเตือนทางอีเมล'],
                        ['notifications[moderation_alerts]', 'notifications.moderation_alerts', 'แจ้งเตือนงานตรวจสอบชุมชน'],
                        ['accessibility[reduced_motion]', 'accessibility.reduced_motion', 'ลดภาพเคลื่อนไหว'],
                        ['accessibility[high_contrast]', 'accessibility.high_contrast', 'เพิ่มความต่างสีสูง'],
                    ] as [$name, $key, $label])
                        <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-slate-950/30 p-4 text-sm text-slate-300">
                            <input type="hidden" name="{{ $name }}" value="0">
                            <input type="checkbox" name="{{ $name }}" value="1" @checked((bool) $pref($key)) class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-700">
                        บันทึกการตั้งค่า
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.admin>
