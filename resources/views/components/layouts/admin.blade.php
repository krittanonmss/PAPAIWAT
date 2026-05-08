<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --admin-bg: #020617;
            --admin-sidebar: #0b1220;
            --admin-surface: rgba(255, 255, 255, 0.04);
            --admin-surface-strong: rgba(15, 23, 42, 0.42);
            --admin-border: rgba(255, 255, 255, 0.10);
            --admin-muted: #94a3b8;
            --admin-blue: #60a5fa;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(30, 64, 175, 0.18), transparent 28rem),
                linear-gradient(180deg, #020617 0%, #0f172a 100%);
        }

        .admin-content {
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 24rem),
                rgba(2, 6, 23, 0.64);
        }

        .admin-content > div > :first-child {
            overflow: hidden;
            border: 1px solid var(--admin-border) !important;
            border-radius: 1.5rem !important;
            background:
                linear-gradient(90deg, rgba(15, 23, 42, 0.96), rgba(15, 23, 42, 0.94), rgba(49, 46, 129, 0.88)) !important;
            box-shadow: 0 20px 42px rgba(2, 6, 23, 0.24) !important;
            backdrop-filter: blur(16px);
        }

        .admin-content > div > :first-child:not([class*=" p-"]):not([class^="p-"]):not([class*=" px-"]):not([class^="px-"]) {
            padding: 1.5rem;
        }

        .admin-content > div > :first-child h1 {
            color: #fff;
        }

        .admin-content > div > :first-child > div:first-child {
            border-color: var(--admin-border);
        }

        .admin-content section,
        .admin-content aside,
        .admin-content form,
        .admin-content article,
        .admin-content div {
            border-color: var(--admin-border);
        }

        .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        .admin-content select,
        .admin-content textarea {
            border-color: var(--admin-border) !important;
            background-color: rgba(2, 6, 23, 0.42) !important;
            color: #fff;
        }

        .admin-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus,
        .admin-content select:focus,
        .admin-content textarea:focus {
            border-color: rgba(96, 165, 250, 0.9) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18) !important;
            outline: none;
        }

        .admin-content table thead {
            background: rgba(2, 6, 23, 0.34);
            color: #94a3b8;
        }

        .admin-content table tbody tr:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .admin-content a,
        .admin-content button {
            transition-property: color, background-color, border-color, opacity, transform, box-shadow;
            transition-duration: 150ms;
        }

        .admin-content .shadow-xl {
            box-shadow: 0 20px 42px rgba(2, 6, 23, 0.24) !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    @php
        $admin = auth('admin')->user();

        $isAccessManagementActive = request()->routeIs('admin.users.*')
            || request()->routeIs('admin.roles.*')
            || request()->routeIs('admin.permissions.*');

        $isTempleManagementActive = request()->routeIs('admin.temples.*');

        $isArticleManagementActive = request()->routeIs('admin.content.articles.*')
            || request()->routeIs('admin.content.article-tags.*');

        $isCategoryManagementActive = request()->routeIs('admin.categories.*');

        $isMediaManagementActive = request()->routeIs('admin.media-folders.*')
            || request()->routeIs('admin.media.*');

        $isLayoutManagementActive = request()->routeIs('admin.content.menus.*')
            || request()->routeIs('admin.content.pages.*')
            || request()->routeIs('admin.content.templates.*');

        $isContentManagementActive = $isTempleManagementActive
            || $isArticleManagementActive
            || $isCategoryManagementActive
            || $isMediaManagementActive
            || $isLayoutManagementActive;
    @endphp

    <div x-data="{ sidebarOpen: true }" class="min-h-screen">
        <div class="flex min-h-screen">
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-slate-950/70 backdrop-blur-sm md:hidden"
                @click="sidebarOpen = false"
            ></div>

            {{-- FIXED: h-screen on aside + flex-col + overflow-hidden so logout is always visible --}}
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0 md:w-0 md:min-w-0 md:border-r-0'"
                class="fixed inset-y-0 left-0 z-40 flex h-screen w-72 flex-col border-r border-white/10 bg-[#0b1220] shadow-2xl shadow-slate-950/40 transition-all duration-300 md:sticky md:top-0 md:h-screen md:translate-x-0"
            >
                <div
                    x-show="sidebarOpen"
                    x-transition
                    class="flex h-full min-h-0 flex-col overflow-hidden"
                >
                    {{-- Logo - shrink-0 ไม่ให้หด --}}
                    <div class="shrink-0 border-b border-white/10 px-5 py-6">
                        <h1 class="text-2xl font-bold tracking-tight text-white">PAPAIWAT</h1>
                        <p class="mt-1 text-sm text-slate-400">ระบบจัดการเว็บไซต์</p>
                    </div>

                    {{-- User info - shrink-0 ไม่ให้หด --}}
                    <div class="shrink-0 border-b border-white/10 px-4 py-5">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-4 shadow-lg shadow-slate-950/20 backdrop-blur">
                            <p class="text-sm font-semibold text-white">
                                {{ $admin?->username ?? '-' }}
                            </p>
                            <p class="mt-1 text-sm text-slate-400">
                                {{ $admin?->role?->name ?? 'ไม่มีบทบาท' }}
                            </p>
                        </div>
                    </div>

                    {{-- Nav - flex-1 + overflow-y-auto ให้ scroll ได้เฉพาะส่วนนี้ --}}
                    <nav class="min-h-0 flex-1 space-y-3 overflow-y-auto px-4 py-5 text-sm">
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="{{ request()->routeIs('admin.dashboard')
                                ? 'block rounded-2xl border border-blue-400/30 bg-blue-900/80 px-4 py-3 font-medium text-blue-300 shadow-md shadow-blue-950/30'
                                : 'block rounded-2xl px-4 py-3 font-medium text-slate-400 hover:bg-white/5 hover:text-white' }}"
                        >
                            แดชบอร์ด
                        </a>

                        <details
                            x-data="{ open: {{ $isContentManagementActive ? 'true' : 'false' }} }"
                            x-bind:open="open"
                            class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03] p-2 shadow-lg shadow-slate-950/20 backdrop-blur"
                        >
                            <summary
                                @click.prevent="open = !open"
                                class="cursor-pointer list-none rounded-xl px-3 py-2.5 font-medium text-slate-300 hover:bg-white/5 hover:text-white"
                            >
                                <div class="flex items-center justify-between">
                                    <span>จัดการเนื้อหา</span>
                                    <span class="text-xs text-slate-500">5</span>
                                </div>
                            </summary>

                            <div class="mt-2 space-y-2 transition-all duration-200">
                                <details
                                    x-data="{ open: {{ $isTempleManagementActive ? 'true' : 'false' }} }"
                                    x-bind:open="open"
                                    class="overflow-hidden rounded-xl border border-white/10 bg-[#0f1727]/70 p-2"
                                >
                                    <summary
                                        @click.prevent="open = !open"
                                        class="cursor-pointer list-none rounded-lg px-3 py-2 font-medium text-slate-400 hover:bg-white/5 hover:text-white"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>จัดการวัด</span>
                                            <span class="text-xs text-slate-500">1</span>
                                        </div>
                                    </summary>

                                    <div class="mt-1 space-y-1">
                                        <a
                                            href="{{ route('admin.temples.index') }}"
                                            class="{{ request()->routeIs('admin.temples.*')
                                                ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                        >
                                            รายการวัด
                                        </a>
                                    </div>
                                </details>

                                <details
                                    x-data="{ open: {{ $isArticleManagementActive ? 'true' : 'false' }} }"
                                    x-bind:open="open"
                                    class="overflow-hidden rounded-xl border border-white/10 bg-[#0f1727]/70 p-2"
                                >
                                    <summary
                                        @click.prevent="open = !open"
                                        class="cursor-pointer list-none rounded-lg px-3 py-2 font-medium text-slate-400 hover:bg-white/5 hover:text-white"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>จัดการบทความ</span>
                                            <span class="text-xs text-slate-500">2</span>
                                        </div>
                                    </summary>

                                    <div class="mt-1 space-y-1">
                                        <a
                                            href="{{ route('admin.content.articles.index') }}"
                                            class="{{ request()->routeIs('admin.content.articles.*')
                                                ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                        >
                                            บทความ
                                        </a>

                                        <a
                                            href="{{ route('admin.content.article-tags.index') }}"
                                            class="{{ request()->routeIs('admin.content.article-tags.*')
                                                ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                        >
                                            แท็กบทความ
                                        </a>
                                    </div>
                                </details>

                                <details
                                    x-data="{ open: {{ $isCategoryManagementActive ? 'true' : 'false' }} }"
                                    x-bind:open="open"
                                    class="overflow-hidden rounded-xl border border-white/10 bg-[#0f1727]/70 p-2"
                                >
                                    <summary
                                        @click.prevent="open = !open"
                                        class="cursor-pointer list-none rounded-lg px-3 py-2 font-medium text-slate-400 hover:bg-white/5 hover:text-white"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>จัดการหมวดหมู่</span>
                                            <span class="text-xs text-slate-500">1</span>
                                        </div>
                                    </summary>

                                    <div class="mt-1 space-y-1">
                                        <a
                                            href="{{ route('admin.categories.index') }}"
                                            class="{{ request()->routeIs('admin.categories.*')
                                                ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                        >
                                            หมวดหมู่
                                        </a>
                                    </div>
                                </details>

                                <details
                                    x-data="{ open: {{ $isMediaManagementActive ? 'true' : 'false' }} }"
                                    x-bind:open="open"
                                    class="overflow-hidden rounded-xl border border-white/10 bg-[#0f1727]/70 p-2"
                                >
                                    <summary
                                        @click.prevent="open = !open"
                                        class="cursor-pointer list-none rounded-lg px-3 py-2 font-medium text-slate-400 hover:bg-white/5 hover:text-white"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>จัดการคลังสื่อ</span>
                                            <span class="text-xs text-slate-500">1</span>
                                        </div>
                                    </summary>

                                    <div class="mt-1 space-y-1">

                                        <a
                                            href="{{ route('admin.media.index') }}"
                                            class="{{ request()->routeIs('admin.media.*')
                                                ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                        >
                                            คลังสื่อ
                                        </a>
                                    </div>
                                </details>

                                <details
                                    x-data="{ open: {{ $isLayoutManagementActive ? 'true' : 'false' }} }"
                                    x-bind:open="open"
                                    class="overflow-hidden rounded-xl border border-white/10 bg-[#0f1727]/70 p-2"
                                >
                                    <summary
                                        @click.prevent="open = !open"
                                        class="cursor-pointer list-none rounded-lg px-3 py-2 font-medium text-slate-400 hover:bg-white/5 hover:text-white"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>จัดการโครงหน้าเว็บ</span>
                                            <span class="text-xs text-slate-500">3</span>
                                        </div>
                                    </summary>

                                    <div class="mt-1 space-y-1">
                                        <a
                                            href="{{ route('admin.content.menus.index') }}"
                                            class="{{ request()->routeIs('admin.content.menus.*')
                                                ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                        >
                                            เมนู
                                        </a>

                                        <a
                                            href="{{ route('admin.content.pages.index') }}"
                                            class="{{ request()->routeIs('admin.content.pages.*')
                                                ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                        >
                                            หน้าเว็บไซต์
                                        </a>

                                        <a
                                            href="{{ route('admin.content.templates.index') }}"
                                            class="{{ request()->routeIs('admin.content.templates.*')
                                                ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                        >
                                            เทมเพลต
                                        </a>
                                    </div>
                                </details>
                            </div>
                        </details>

                        <details
                            x-data="{ open: {{ $isAccessManagementActive ? 'true' : 'false' }} }"
                            x-bind:open="open"
                            class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03] p-2 shadow-lg shadow-slate-950/20 backdrop-blur"
                        >
                            <summary
                                @click.prevent="open = !open"
                                class="cursor-pointer list-none rounded-xl px-3 py-2.5 font-medium text-slate-300 hover:bg-white/5 hover:text-white"
                            >
                                <div class="flex items-center justify-between">
                                    <span>จัดการสิทธิ์ผู้ใช้งาน</span>
                                    <span class="text-xs text-slate-500">2</span>
                                </div>
                            </summary>

                            <div class="mt-2 space-y-1">
                                <a
                                    href="{{ route('admin.users.index') }}"
                                    class="{{ request()->routeIs('admin.users.*')
                                        ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                        : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                >
                                    ผู้ใช้งาน
                                </a>

                                <a
                                    href="{{ route('admin.roles.index') }}"
                                    class="{{ request()->routeIs('admin.roles.*')
                                        ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                        : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                >
                                    บทบาทผู้ใช้งาน
                                </a>

                            </div>
                        </details>

                        <a
                            href="{{ route('admin.profile.edit') }}"
                            class="{{ request()->routeIs('admin.profile.*')
                                ? 'block rounded-2xl border border-blue-400/30 bg-blue-900/80 px-4 py-3 font-medium text-blue-300 shadow-md shadow-blue-950/30'
                                : 'block rounded-2xl px-4 py-3 font-medium text-slate-400 hover:bg-white/5 hover:text-white' }}"
                        >
                            โปรไฟล์ของฉัน
                        </a>
                        
                    </nav>

                    {{-- Logout - shrink-0 ทำให้ติดล่างเสมอ ไม่ถูกดันออก --}}
                    <div class="shrink-0 border-t border-white/10 px-4 py-4">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-2xl px-4 py-3 text-left text-sm font-semibold text-red-400 hover:bg-red-500/10 hover:text-red-300"
                            >
                                ออกจากระบบ
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="flex min-h-screen min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 border-b border-white/10 bg-[#0b1220]/90 shadow-lg shadow-slate-950/10 backdrop-blur">
                    <div class="flex items-center justify-between px-5 py-4 md:px-6">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="sidebarOpen = !sidebarOpen"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 text-slate-300 hover:bg-white/5 hover:text-white"
                                aria-label="Toggle sidebar"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div>
                                <h2 class="text-xl font-semibold text-white">
                                    {{ $header ?? 'แดชบอร์ด' }}
                                </h2>
                            </div>
                        </div>

                        <a
                            href="{{ route('admin.profile.edit') }}"
                            class="hidden rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-sm text-slate-300 transition hover:bg-white/10 hover:text-white sm:inline-flex"
                        >
                            {{ $admin?->email ?? '-' }}
                        </a>
                    </div>
                </header>

                <main class="admin-content flex-1 p-5 md:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
</body>
</html>
