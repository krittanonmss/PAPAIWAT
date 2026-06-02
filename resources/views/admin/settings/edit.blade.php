<x-layouts.admin title="ตั้งค่าเว็บไซต์" header="ตั้งค่าเว็บไซต์">
    @php
        $current = $settings[$activeTab] ?? [];
        $value = fn (string $key, mixed $fallback = null) => old('settings.'.$key, $current[$key] ?? $fallback);
        $field = 'w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
        $check = 'h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600 focus:ring-blue-500';
    @endphp

    <div class="space-y-5 text-white">
        <div class="px-6 py-6">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-300">SITE SETTINGS</p>
            <h1 class="mt-2 text-2xl font-bold text-white">ตั้งค่าเว็บไซต์</h1>
            <p class="mt-1 text-sm text-slate-300">ค่ากลางสำหรับเนื้อหา การเผยแพร่ การเชื่อมต่อ และการดูแลระบบ</p>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-3 text-sm text-red-300">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <nav class="sticky top-4 z-20 rounded-3xl border border-white/10 bg-slate-950/90 p-2 shadow-xl shadow-slate-950/30 backdrop-blur" aria-label="Settings tabs">
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($tabs as $tab => $label)
                    <a href="{{ route('admin.settings.edit', ['tab' => $tab]) }}"
                        @class([
                            'rounded-2xl border px-4 py-3 text-center text-sm font-semibold transition',
                            'border-blue-400/30 bg-blue-500/20 text-blue-100' => $activeTab === $tab,
                            'border-transparent text-slate-400 hover:bg-white/[0.06] hover:text-white' => $activeTab !== $tab,
                        ])
                        @if ($activeTab === $tab) aria-current="page" @endif>
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </nav>

        @if ($activeTab !== 'audit')
            <form method="POST" action="{{ route('admin.settings.update', $activeTab) }}" class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                @csrf
                @method('PUT')

                @if ($activeTab === 'general')
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="site_name" class="mb-1.5 block text-sm text-slate-300">ชื่อเว็บไซต์</label>
                            <input id="site_name" name="settings[site_name]" value="{{ $value('site_name') }}" class="{{ $field }}" required>
                        </div>
                        <div>
                            <label for="tagline" class="mb-1.5 block text-sm text-slate-300">Tagline</label>
                            <input id="tagline" name="settings[tagline]" value="{{ $value('tagline') }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="contact_email" class="mb-1.5 block text-sm text-slate-300">อีเมลติดต่อ</label>
                            <input id="contact_email" type="email" name="settings[contact_email]" value="{{ $value('contact_email') }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="contact_phone" class="mb-1.5 block text-sm text-slate-300">โทรศัพท์</label>
                            <input id="contact_phone" name="settings[contact_phone]" value="{{ $value('contact_phone') }}" class="{{ $field }}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="contact_address" class="mb-1.5 block text-sm text-slate-300">ที่อยู่ติดต่อ</label>
                            <textarea id="contact_address" name="settings[contact_address]" rows="3" class="{{ $field }}">{{ $value('contact_address') }}</textarea>
                        </div>
                        <div>
                            <label for="locale" class="mb-1.5 block text-sm text-slate-300">ภาษาเริ่มต้น</label>
                            <select id="locale" name="settings[locale]" class="{{ $field }}"><option value="th" @selected($value('locale') === 'th')>ไทย</option><option value="en" @selected($value('locale') === 'en')>English</option></select>
                        </div>
                        <div>
                            <label for="timezone" class="mb-1.5 block text-sm text-slate-300">Timezone</label>
                            <select id="timezone" name="settings[timezone]" class="{{ $field }}"><option value="Asia/Bangkok" @selected($value('timezone') === 'Asia/Bangkok')>Asia/Bangkok</option><option value="UTC" @selected($value('timezone') === 'UTC')>UTC</option></select>
                        </div>
                    </div>
                @elseif ($activeTab === 'seo')
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="default_title" class="mb-1.5 block text-sm text-slate-300">Default title</label>
                            <input id="default_title" name="settings[default_title]" value="{{ $value('default_title') }}" class="{{ $field }}" required>
                        </div>
                        <div>
                            <label for="canonical_base_url" class="mb-1.5 block text-sm text-slate-300">Canonical base URL</label>
                            <input id="canonical_base_url" type="url" name="settings[canonical_base_url]" value="{{ $value('canonical_base_url') }}" class="{{ $field }}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="default_description" class="mb-1.5 block text-sm text-slate-300">Default meta description</label>
                            <textarea id="default_description" name="settings[default_description]" rows="3" class="{{ $field }}">{{ $value('default_description') }}</textarea>
                        </div>
                        <div>
                            <label for="og_image_media_id" class="mb-1.5 block text-sm text-slate-300">Default OG image</label>
                            <select id="og_image_media_id" name="settings[og_image_media_id]" class="{{ $field }}">
                                <option value="">ไม่กำหนด</option>
                                @foreach ($mediaImages as $image)
                                    <option value="{{ $image->id }}" @selected((string) $value('og_image_media_id') === (string) $image->id)>{{ $image->title ?: $image->original_filename }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="mt-7 flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-3 text-sm text-slate-200">
                            <input type="hidden" name="settings[indexing_enabled]" value="0">
                            <input type="checkbox" name="settings[indexing_enabled]" value="1" @checked((bool) $value('indexing_enabled')) class="{{ $check }}">
                            อนุญาตให้ search engines index เว็บไซต์
                        </label>
                    </div>
                @elseif ($activeTab === 'content')
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="temple_default_template_id" class="mb-1.5 block text-sm text-slate-300">Temple detail template เริ่มต้น</label>
                            <select id="temple_default_template_id" name="settings[temple_default_template_id]" class="{{ $field }}">
                                <option value="">ใช้ค่า default ของ Template System</option>
                                @foreach ($templates->where('content_type', 'temple') as $template)
                                    <option value="{{ $template->id }}" @selected((string) $value('temple_default_template_id') === (string) $template->id)>{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="article_default_template_id" class="mb-1.5 block text-sm text-slate-300">Article detail template เริ่มต้น</label>
                            <select id="article_default_template_id" name="settings[article_default_template_id]" class="{{ $field }}">
                                <option value="">ใช้ค่า default ของ Template System</option>
                                @foreach ($templates->where('content_type', 'article') as $template)
                                    <option value="{{ $template->id }}" @selected((string) $value('article_default_template_id') === (string) $template->id)>{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="default_status" class="mb-1.5 block text-sm text-slate-300">สถานะเริ่มต้นของเนื้อหาใหม่</label>
                            <select id="default_status" name="settings[default_status]" class="{{ $field }}"><option value="draft" @selected($value('default_status') === 'draft')>Draft</option><option value="review" @selected($value('default_status') === 'review')>Review</option></select>
                        </div>
                        <div class="space-y-3">
                            @foreach (['article_allow_comments_default' => 'เปิดความคิดเห็นในบทความใหม่', 'temple_reviews_enabled' => 'เปิดรีวิวบนหน้าวัด'] as $key => $label)
                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-3 text-sm text-slate-200">
                                    <input type="hidden" name="settings[{{ $key }}]" value="0">
                                    <input type="checkbox" name="settings[{{ $key }}]" value="1" @checked((bool) $value($key)) class="{{ $check }}">{{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @elseif ($activeTab === 'moderation')
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="space-y-3">
                            @foreach (['comments_enabled' => 'รับความคิดเห็นจากหน้าเว็บ', 'reviews_enabled' => 'รับรีวิววัดจากหน้าเว็บ', 'reports_enabled' => 'รับรายงานความคิดเห็นและรีวิว'] as $key => $label)
                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-3 text-sm text-slate-200">
                                    <input type="hidden" name="settings[{{ $key }}]" value="0">
                                    <input type="checkbox" name="settings[{{ $key }}]" value="1" @checked((bool) $value($key)) class="{{ $check }}">{{ $label }}
                                </label>
                            @endforeach
                        </div>
                        <div class="space-y-5">
                            <div>
                                <label for="auto_hide_report_threshold" class="mb-1.5 block text-sm text-slate-300">ซ่อนอัตโนมัติเมื่อถูกรายงานครบ</label>
                                <input id="auto_hide_report_threshold" type="number" min="1" max="20" name="settings[auto_hide_report_threshold]" value="{{ $value('auto_hide_report_threshold') }}" class="{{ $field }}" required>
                            </div>
                            <div>
                                <label for="notification_email" class="mb-1.5 block text-sm text-slate-300">อีเมลรับแจ้ง moderation</label>
                                <input id="notification_email" type="email" name="settings[notification_email]" value="{{ $value('notification_email') }}" class="{{ $field }}">
                            </div>
                        </div>
                    </div>
                @elseif ($activeTab === 'media')
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="max_upload_mb" class="mb-1.5 block text-sm text-slate-300">ขนาด upload สูงสุด (MB)</label>
                            <input id="max_upload_mb" type="number" min="1" max="20" name="settings[max_upload_mb]" value="{{ $value('max_upload_mb') }}" class="{{ $field }}" required>
                        </div>
                        <div>
                            <label for="default_visibility" class="mb-1.5 block text-sm text-slate-300">Visibility เริ่มต้น</label>
                            <select id="default_visibility" name="settings[default_visibility]" class="{{ $field }}"><option value="public" @selected($value('default_visibility') === 'public')>Public</option><option value="private" @selected($value('default_visibility') === 'private')>Private</option></select>
                        </div>
                        <div>
                            <p class="mb-2 text-sm text-slate-300">ชนิดไฟล์ที่รับ</p>
                            <div class="flex gap-3">
                                @foreach (['image' => 'Images', 'document' => 'PDF documents'] as $key => $label)
                                    <label class="flex flex-1 items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-3 text-sm text-slate-200"><input type="checkbox" name="settings[allowed_types][]" value="{{ $key }}" @checked(in_array($key, (array) $value('allowed_types'), true)) class="{{ $check }}">{{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label for="image_quality" class="mb-1.5 block text-sm text-slate-300">คุณภาพ image variant (%)</label>
                            <input id="image_quality" type="number" min="40" max="100" name="settings[image_quality]" value="{{ $value('image_quality') }}" class="{{ $field }}" required>
                        </div>
                        <div>
                            <label for="duplicate_policy" class="mb-1.5 block text-sm text-slate-300">การจัดการไฟล์ซ้ำ</label>
                            <select id="duplicate_policy" name="settings[duplicate_policy]" class="{{ $field }}"><option value="reject" @selected($value('duplicate_policy') === 'reject')>ปฏิเสธไฟล์ซ้ำ</option><option value="allow" @selected($value('duplicate_policy') === 'allow')>อนุญาตไฟล์ซ้ำ</option></select>
                        </div>
                    </div>
                @elseif ($activeTab === 'navigation')
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="header_menu_id" class="mb-1.5 block text-sm text-slate-300">Header menu</label>
                            <select id="header_menu_id" name="settings[header_menu_id]" class="{{ $field }}"><option value="">ใช้เมนู default</option>@foreach ($menus->where('location_key', 'header') as $menu)<option value="{{ $menu->id }}" @selected((string) $value('header_menu_id') === (string) $menu->id)>{{ $menu->name }}</option>@endforeach</select>
                        </div>
                        <div>
                            <label for="footer_menu_id" class="mb-1.5 block text-sm text-slate-300">Footer menu</label>
                            <select id="footer_menu_id" name="settings[footer_menu_id]" class="{{ $field }}"><option value="">ใช้เมนู default</option>@foreach ($menus->where('location_key', 'footer') as $menu)<option value="{{ $menu->id }}" @selected((string) $value('footer_menu_id') === (string) $menu->id)>{{ $menu->name }}</option>@endforeach</select>
                        </div>
                        @foreach (['facebook_url' => 'Facebook URL', 'instagram_url' => 'Instagram URL', 'youtube_url' => 'YouTube URL', 'line_url' => 'LINE URL'] as $key => $label)
                            <div>
                                <label for="{{ $key }}" class="mb-1.5 block text-sm text-slate-300">{{ $label }}</label>
                                <input id="{{ $key }}" type="url" name="settings[{{ $key }}]" value="{{ $value($key) }}" class="{{ $field }}">
                            </div>
                        @endforeach
                        <div class="md:col-span-2">
                            <a href="{{ route('admin.content.footer.edit') }}" class="inline-flex rounded-xl border border-white/10 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/10 hover:text-white">ปรับรูปแบบและข้อความ Footer</a>
                        </div>
                    </div>
                @elseif ($activeTab === 'integrations')
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="analytics_measurement_id" class="mb-1.5 block text-sm text-slate-300">Google Analytics Measurement ID</label>
                            <input id="analytics_measurement_id" name="settings[analytics_measurement_id]" placeholder="G-XXXXXXXXXX" value="{{ $value('analytics_measurement_id') }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="tag_manager_container_id" class="mb-1.5 block text-sm text-slate-300">Google Tag Manager Container ID</label>
                            <input id="tag_manager_container_id" name="settings[tag_manager_container_id]" placeholder="GTM-XXXXXXX" value="{{ $value('tag_manager_container_id') }}" class="{{ $field }}">
                        </div>
                        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-3 text-sm text-slate-200">
                            <input type="hidden" name="settings[maps_enabled]" value="0">
                            <input type="checkbox" name="settings[maps_enabled]" value="1" @checked((bool) $value('maps_enabled')) class="{{ $check }}">เปิดการเชื่อมต่อ Google Maps
                        </label>
                        <div>
                            <label for="maps_public_browser_key" class="mb-1.5 block text-sm text-slate-300">Maps browser key (public key)</label>
                            <input id="maps_public_browser_key" name="settings[maps_public_browser_key]" value="{{ $value('maps_public_browser_key') }}" class="{{ $field }}">
                        </div>
                    </div>
                @elseif ($activeTab === 'maintenance')
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-3 text-sm text-slate-200">
                            <input type="hidden" name="settings[announcement_enabled]" value="0">
                            <input type="checkbox" name="settings[announcement_enabled]" value="1" @checked((bool) $value('announcement_enabled')) class="{{ $check }}">แสดง announcement banner หน้าเว็บ
                        </label>
                        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-3 text-sm text-slate-200">
                            <input type="hidden" name="settings[sitemap_enabled]" value="0">
                            <input type="checkbox" name="settings[sitemap_enabled]" value="1" @checked((bool) $value('sitemap_enabled')) class="{{ $check }}">เปิดใช้งาน sitemap generation
                        </label>
                        <div class="md:col-span-2">
                            <label for="announcement_text" class="mb-1.5 block text-sm text-slate-300">ข้อความ banner</label>
                            <input id="announcement_text" name="settings[announcement_text]" value="{{ $value('announcement_text') }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="announcement_level" class="mb-1.5 block text-sm text-slate-300">ระดับ banner</label>
                            <select id="announcement_level" name="settings[announcement_level]" class="{{ $field }}"><option value="info" @selected($value('announcement_level') === 'info')>Info</option><option value="warning" @selected($value('announcement_level') === 'warning')>Warning</option><option value="critical" @selected($value('announcement_level') === 'critical')>Critical</option></select>
                        </div>
                        <div class="text-sm text-slate-400">สร้าง sitemap ล่าสุด: {{ $value('sitemap_last_generated_at') ?: 'ยังไม่เคยสร้าง' }}</div>
                    </div>
                @endif

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">บันทึก {{ $tabs[$activeTab] }}</button>
                </div>
            </form>

            @if ($activeTab === 'maintenance' && auth('admin')->user()?->hasPermission('settings.maintenance'))
                <section class="flex flex-wrap gap-3 rounded-3xl border border-white/10 bg-white/[0.04] p-6">
                    <form method="POST" action="{{ route('admin.settings.maintenance.cache') }}">@csrf<button class="rounded-xl border border-white/10 px-4 py-2.5 text-sm text-slate-200 hover:bg-white/10">ล้าง cache หน้าเว็บ</button></form>
                    <form method="POST" action="{{ route('admin.settings.maintenance.sitemap') }}">@csrf<button class="rounded-xl border border-white/10 px-4 py-2.5 text-sm text-slate-200 hover:bg-white/10">สร้าง sitemap.xml</button></form>
                </section>
            @endif
        @else
            <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead><tr><th class="px-5 py-3 text-left">เวลา</th><th class="px-5 py-3 text-left">Action</th><th class="px-5 py-3 text-left">ผู้ดำเนินการ</th><th class="px-5 py-3 text-left">ค่าใหม่</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($auditLogs ?? [] as $log)
                            <tr><td class="px-5 py-4 text-slate-400">{{ $log->created_at?->format('d/m/Y H:i') }}</td><td class="px-5 py-4 text-slate-200">{{ $log->action }}</td><td class="px-5 py-4 text-slate-300">{{ $log->performer?->username ?? '-' }}</td><td class="max-w-md truncate px-5 py-4 text-slate-400">{{ json_encode($log->new_data, JSON_UNESCAPED_UNICODE) }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">ยังไม่มีประวัติการแก้ settings</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($auditLogs)
                    <div class="border-t border-white/10 px-5 py-4">{{ $auditLogs->links() }}</div>
                @endif
            </section>
        @endif
    </div>
</x-layouts.admin>
