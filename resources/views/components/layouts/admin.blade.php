<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    @php
        $isAccessManagementActive = request()->routeIs('admin.users.*')
            || request()->routeIs('admin.roles.*')
            || request()->routeIs('admin.permissions.*');
    @endphp

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="w-64 bg-white border-r border-slate-200 hidden md:block">
            <div class="p-6 border-b border-slate-200">
                <h1 class="text-lg font-bold">PAPAIWAT</h1>
                <p class="text-sm text-slate-500">Admin Panel</p>
            </div>

            <nav class="p-4 space-y-2 text-sm">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard')
                        ? 'block rounded-lg bg-slate-900 px-3 py-2 text-white'
                        : 'block rounded-lg px-3 py-2 hover:bg-slate-100' }}"
                >
                    Dashboard
                </a>

                <details class="rounded-lg border border-slate-200 bg-slate-50" {{ $isAccessManagementActive ? 'open' : '' }}>
                    <summary class="cursor-pointer list-none px-3 py-2 font-medium text-slate-800">
                        <div class="flex items-center justify-between">
                            <span>Access Management</span>
                            <span class="text-xs text-slate-500">3</span>
                        </div>
                    </summary>

                    <div class="mt-1 space-y-1 px-2 pb-2">
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="{{ request()->routeIs('admin.users.*')
                                ? 'block rounded-lg bg-slate-900 px-3 py-2 text-white'
                                : 'block rounded-lg px-3 py-2 hover:bg-white' }}"
                        >
                            User Management
                        </a>

                        <a
                            href="{{ route('admin.roles.index') }}"
                            class="{{ request()->routeIs('admin.roles.*')
                                ? 'block rounded-lg bg-slate-900 px-3 py-2 text-white'
                                : 'block rounded-lg px-3 py-2 hover:bg-white' }}"
                        >
                            Role Management
                        </a>

                        <a
                            href="{{ route('admin.permissions.index') }}"
                            class="{{ request()->routeIs('admin.permissions.*')
                                ? 'block rounded-lg bg-slate-900 px-3 py-2 text-white'
                                : 'block rounded-lg px-3 py-2 hover:bg-white' }}"
                        >
                            Permission Management
                        </a>
                    </div>
                </details>
            </nav>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col">

            {{-- Header --}}
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">
                        {{ $header ?? 'Dashboard' }}
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600">
                        {{ auth('admin')->user()?->username }}
                    </span>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="text-sm px-3 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            {{-- Content --}}
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>