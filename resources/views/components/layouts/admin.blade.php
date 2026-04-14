<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    @php
        $isAccessManagementActive = request()->routeIs('admin.users.*')
            || request()->routeIs('admin.roles.*')
            || request()->routeIs('admin.permissions.*');

        $isCategoryManagementActive = request()->routeIs('admin.categories.*');

        $isMediaManagementActive = request()->routeIs('admin.media-folders.*')
            || request()->routeIs('admin.media.*');

        $isContentManagementActive = $isCategoryManagementActive || $isMediaManagementActive;

        $admin = auth('admin')->user();
    @endphp

    <div x-data="{ sidebarOpen: true }" class="min-h-screen">
        <div class="flex min-h-screen">
            {{-- Sidebar Overlay (mobile) --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-slate-900/30 md:hidden"
                @click="sidebarOpen = false"
            ></div>

            {{-- Sidebar --}}
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0 md:w-0 md:min-w-0 md:border-r-0'"
                class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-slate-200 bg-white transition-all duration-300 md:static md:translate-x-0"
            >
                <div
                    x-show="sidebarOpen"
                    x-transition
                    class="flex h-full flex-col"
                >
                    {{-- Brand --}}
                    <div class="px-5 py-5">
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">PAPAIWAT</h1>
                        <p class="text-sm text-slate-500">Admin Panel</p>
                    </div>

                    {{-- User Info --}}
                    <div class="px-4 pb-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $admin?->username ?? '-' }}
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                ({{ $admin?->role?->name ?? 'No Role' }})
                            </p>
                        </div>
                    </div>

                    {{-- Menu --}}
                    <nav class="flex-1 space-y-2 overflow-y-auto px-4 pb-4 text-sm">
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="{{ request()->routeIs('admin.dashboard')
                                ? 'block rounded-xl bg-slate-900 px-4 py-3 font-medium text-white'
                                : 'block rounded-xl px-4 py-3 text-slate-700 hover:bg-slate-100' }}"
                        >
                            Dashboard
                        </a>

                        <details class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50" {{ $isContentManagementActive ? 'open' : '' }}>
                            <summary class="cursor-pointer list-none px-4 py-3 font-medium text-slate-800">
                                <div class="flex items-center justify-between">
                                    <span>Content Management</span>
                                    <span class="text-xs text-slate-500">2</span>
                                </div>
                            </summary>

                            <div class="space-y-1 px-2 pb-2">
                                <a
                                    href="{{ route('admin.categories.index') }}"
                                    class="{{ request()->routeIs('admin.categories.*')
                                        ? 'block rounded-lg bg-slate-900 px-3 py-2.5 text-white'
                                        : 'block rounded-lg px-3 py-2.5 text-slate-700 hover:bg-white' }}"
                                >
                                    Category Management
                                </a>

                                <details class="overflow-hidden rounded-lg border border-slate-200 bg-white" {{ $isMediaManagementActive ? 'open' : '' }}>
                                    <summary class="cursor-pointer list-none px-3 py-2.5 font-medium text-slate-800">
                                        <div class="flex items-center justify-between">
                                            <span>Media Management</span>
                                            <span class="text-xs text-slate-500">2</span>
                                        </div>
                                    </summary>

                                    <div class="space-y-1 px-2 pb-2">
                                        <a
                                            href="{{ route('admin.media-folders.index') }}"
                                            class="{{ request()->routeIs('admin.media-folders.*')
                                                ? 'block rounded-lg bg-slate-900 px-3 py-2.5 text-white'
                                                : 'block rounded-lg px-3 py-2.5 text-slate-700 hover:bg-slate-50' }}"
                                        >
                                            Media Folder Management
                                        </a>

                                        <a
                                            href="{{ route('admin.media.index') }}"
                                            class="{{ request()->routeIs('admin.media.*')
                                                ? 'block rounded-lg bg-slate-900 px-3 py-2.5 text-white'
                                                : 'block rounded-lg px-3 py-2.5 text-slate-700 hover:bg-slate-50' }}"
                                        >
                                            Media Library
                                        </a>
                                    </div>
                                </details>
                            </div>
                        </details>

                        <details class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50" {{ $isAccessManagementActive ? 'open' : '' }}>
                            <summary class="cursor-pointer list-none px-4 py-3 font-medium text-slate-800">
                                <div class="flex items-center justify-between">
                                    <span>Access Management</span>
                                    <span class="text-xs text-slate-500">3</span>
                                </div>
                            </summary>

                            <div class="space-y-1 px-2 pb-2">
                                <a
                                    href="{{ route('admin.users.index') }}"
                                    class="{{ request()->routeIs('admin.users.*')
                                        ? 'block rounded-lg bg-slate-900 px-3 py-2.5 text-white'
                                        : 'block rounded-lg px-3 py-2.5 text-slate-700 hover:bg-white' }}"
                                >
                                    User Management
                                </a>

                                <a
                                    href="{{ route('admin.roles.index') }}"
                                    class="{{ request()->routeIs('admin.roles.*')
                                        ? 'block rounded-lg bg-slate-900 px-3 py-2.5 text-white'
                                        : 'block rounded-lg px-3 py-2.5 text-slate-700 hover:bg-white' }}"
                                >
                                    Role Management
                                </a>

                                <a
                                    href="{{ route('admin.permissions.index') }}"
                                    class="{{ request()->routeIs('admin.permissions.*')
                                        ? 'block rounded-lg bg-slate-900 px-3 py-2.5 text-white'
                                        : 'block rounded-lg px-3 py-2.5 text-slate-700 hover:bg-white' }}"
                                >
                                    Permission Management
                                </a>
                            </div>
                        </details>
                    </nav>

                    {{-- Footer Logout --}}
                    <div class="mt-auto px-4 pb-4 pt-3">
                        <div class="border-t border-slate-200 pt-4">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-medium text-white hover:bg-slate-800"
                                >
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Main --}}
            <div
                :class="sidebarOpen ? 'md:ml-0' : 'md:ml-0'"
                class="flex min-h-screen min-w-0 flex-1 flex-col"
            >
                {{-- Header --}}
                <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div class="flex items-center justify-between px-5 py-4 md:px-6">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="sidebarOpen = !sidebarOpen"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100"
                                aria-label="Toggle sidebar"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">
                                    {{ $header ?? 'Dashboard' }}
                                </h2>
                            </div>
                        </div>

                        <div class="text-sm text-slate-500">
                            {{ $admin?->email ?? '-' }}
                        </div>
                    </div>
                </header>

                {{-- Content --}}
                <main class="flex-1 p-5 md:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
</body>
</html>