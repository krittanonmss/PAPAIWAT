<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    @include('components.layouts.admin.styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    @php
        $admin = auth('admin')->user();
        $adminPreferences = app(\App\Services\Admin\AdminPreferenceService::class)->forAdmin($admin);
        $adminTheme = in_array($adminPreferences['display.theme'] ?? 'dark', ['dark', 'light', 'system'], true)
            ? $adminPreferences['display.theme']
            : 'dark';
        $adminScale = (int) ($adminPreferences['display.scale'] ?? 80);
        $adminDensity = $adminPreferences['display.density'] ?? 'comfortable';
        $adminSidebarCollapsed = (bool) ($adminPreferences['display.sidebar_collapsed'] ?? false);
        $adminReducedMotion = (bool) ($adminPreferences['accessibility.reduced_motion'] ?? false);
        $adminHighContrast = (bool) ($adminPreferences['accessibility.high_contrast'] ?? false);
        $adminOpenDetailInNewTab = (bool) ($adminPreferences['tables.open_detail_in_new_tab'] ?? false);
        $adminInAppNotifications = (bool) ($adminPreferences['notifications.in_app'] ?? true);
        $adminModerationAlerts = (bool) ($adminPreferences['notifications.moderation_alerts'] ?? true);
        $adminUnreadNotifications = collect();

        if ($admin && $adminInAppNotifications) {
            $adminUnreadNotifications = \App\Models\Admin\AdminNotification::query()
                ->where('admin_id', $admin->id)
                ->where('is_read', false)
                ->when(! $adminModerationAlerts, fn ($query) => $query->where('type', '!=', 'moderation'))
                ->latest('created_at')
                ->limit(5)
                ->get();
        }
        $can = fn (string $permission): bool => $admin?->hasPermission($permission) ?? false;

        $canViewDashboard = $can('dashboard.view');
        $canViewUsers = $can('users.view');
        $canViewRoles = $can('roles.view');
        $canViewTemples = $can('temples.view');
        $canViewArticles = $can('articles.view');
        $canViewCategories = $can('categories.view');
        $canViewMedia = $can('media.view');
        $canViewMenus = $can('menus.view');
        $canViewPages = $can('pages.view');
        $canViewInteractions = $can('interactions.view');
        $canBanInteractions = $can('interactions.ban');
        $canViewSettings = $can('settings.view');

        $isAccountManagementเปิดใช้งาน = request()->routeIs('admin.profile.*')
            || request()->routeIs('admin.preferences.*');

        $isAccessManagementเปิดใช้งาน = request()->routeIs('admin.users.*')
            || request()->routeIs('admin.roles.*')
            || request()->routeIs('admin.settings.*');

        $isTempleManagementเปิดใช้งาน = request()->routeIs('admin.temples.*');

        $isArticleManagementเปิดใช้งาน = request()->routeIs('admin.content.articles.*')
            || request()->routeIs('admin.content.article-tags.*');

        $isCategoryManagementเปิดใช้งาน = request()->routeIs('admin.categories.*');

        $isMediaManagementเปิดใช้งาน = request()->routeIs('admin.media-folders.*')
            || request()->routeIs('admin.media.*');

        $isLayoutManagementเปิดใช้งาน = request()->routeIs('admin.content.menus.*')
            || request()->routeIs('admin.content.pages.*')
            || request()->routeIs('admin.content.templates.*')
            || request()->routeIs('admin.content.footer.*');

        $isInteractionManagementเปิดใช้งาน = request()->routeIs('admin.interactions.*');

        $isContentManagementเปิดใช้งาน = $isTempleManagementเปิดใช้งาน
            || $isArticleManagementเปิดใช้งาน
            || $isCategoryManagementเปิดใช้งาน;

        $sidebarGroups = [
            [
                'label' => 'จัดการเนื้อหา',
                'active' => $isContentManagementเปิดใช้งาน,
                'items' => array_values(array_filter([
                    $canViewTemples ? [
                        'label' => 'จัดการวัด',
                        'active' => $isTempleManagementเปิดใช้งาน,
                        'items' => [
                            ['label' => 'รายการวัด', 'route' => 'admin.temples.index', 'active' => 'admin.temples.*'],
                        ],
                    ] : null,
                    $canViewArticles ? [
                        'label' => 'จัดการบทความ',
                        'active' => $isArticleManagementเปิดใช้งาน,
                        'items' => [
                            ['label' => 'บทความ', 'route' => 'admin.content.articles.index', 'active' => 'admin.content.articles.*'],
                            ['label' => 'แท็กบทความ', 'route' => 'admin.content.article-tags.index', 'active' => 'admin.content.article-tags.*'],
                        ],
                    ] : null,
                    $canViewCategories ? [
                        'label' => 'จัดการหมวดหมู่',
                        'active' => $isCategoryManagementเปิดใช้งาน,
                        'items' => [
                            ['label' => 'หมวดหมู่', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*'],
                        ],
                    ] : null,
                ])),
            ],
            [
                'label' => 'โครงสร้างเว็บไซต์',
                'active' => $isLayoutManagementเปิดใช้งาน,
                'items' => array_values(array_filter([
                    $canViewPages ? ['label' => 'หน้าเว็บไซต์', 'route' => 'admin.content.pages.index', 'active' => 'admin.content.pages.*'] : null,
                    $canViewMenus ? ['label' => 'เมนู', 'route' => 'admin.content.menus.index', 'active' => 'admin.content.menus.*'] : null,
                    $canViewMenus ? ['label' => 'Footer', 'route' => 'admin.content.footer.edit', 'active' => 'admin.content.footer.*'] : null,
                ])),
            ],
            [
                'label' => 'จัดการคลังสื่อ',
                'active' => $isMediaManagementเปิดใช้งาน,
                'items' => array_values(array_filter([
                    $canViewMedia ? ['label' => 'คลังสื่อ', 'route' => 'admin.media.index', 'active' => 'admin.media.*'] : null,
                ])),
            ],
            [
                'label' => 'ตรวจสอบชุมชน',
                'active' => $isInteractionManagementเปิดใช้งาน,
                'items' => array_values(array_filter([
                    $canViewInteractions ? ['label' => 'รีวิววัด', 'route' => 'admin.interactions.reviews.index', 'active' => 'admin.interactions.reviews.*'] : null,
                    $canViewInteractions ? ['label' => 'ความคิดเห็น', 'route' => 'admin.interactions.comments.index', 'active' => 'admin.interactions.comments.*'] : null,
                    $canViewInteractions ? ['label' => 'รายงาน', 'route' => 'admin.interactions.reports.index', 'active' => 'admin.interactions.reports.*'] : null,
                    $canBanInteractions ? ['label' => 'บล็อกผู้เยี่ยมชม', 'route' => 'admin.interactions.bans.index', 'active' => 'admin.interactions.bans.*'] : null,
                ])),
            ],
            [
                'label' => 'ระบบและสิทธิ์',
                'active' => $isAccessManagementเปิดใช้งาน,
                'items' => array_values(array_filter([
                    $canViewUsers ? ['label' => 'ผู้ใช้งาน', 'route' => 'admin.users.index', 'active' => 'admin.users.*'] : null,
                    $canViewRoles ? ['label' => 'บทบาทผู้ใช้งาน', 'route' => 'admin.roles.index', 'active' => 'admin.roles.*'] : null,
                    $canViewSettings ? ['label' => 'ตั้งค่าเว็บไซต์', 'route' => 'admin.settings.edit', 'active' => 'admin.settings.*'] : null,
                ])),
            ],
            [
                'label' => 'บัญชีของฉัน',
                'active' => $isAccountManagementเปิดใช้งาน,
                'items' => [
                    ['label' => 'โปรไฟล์ของฉัน', 'route' => 'admin.profile.edit', 'active' => 'admin.profile.*'],
                    ['label' => 'การตั้งค่าส่วนตัว', 'route' => 'admin.preferences.edit', 'active' => 'admin.preferences.*'],
                ],
            ],
        ];
    @endphp

    @include('components.layouts.admin.preferences')

    <div x-data="{ sidebarOpen: @js(! $adminSidebarCollapsed) }" class="min-h-screen">
        <div class="flex min-h-screen">
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-slate-950/70 backdrop-blur-sm md:hidden"
                @click="sidebarOpen = false"
            ></div>

            @include('components.layouts.admin.sidebar')

            <div class="flex min-h-screen min-w-0 flex-1 flex-col">
                @include('components.layouts.admin.topbar')

                <main class="admin-content flex-1 p-5 md:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
    @include('components.layouts.admin.scripts')
</body>
</html>
