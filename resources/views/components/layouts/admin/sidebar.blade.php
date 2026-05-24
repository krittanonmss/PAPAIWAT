<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0 md:w-0 md:min-w-0 md:border-r-0'"
    class="fixed inset-y-0 left-0 z-40 flex h-screen w-72 flex-col border-r border-white/10 bg-[#0b1220] shadow-2xl shadow-slate-950/40 transition-all duration-300 md:sticky md:top-0 md:h-screen md:translate-x-0"
>
    <div
        x-show="sidebarOpen"
        x-transition
        class="flex h-full min-h-0 flex-col overflow-hidden"
    >
        <div class="shrink-0 border-b border-white/10 px-5 py-6">
            <h1 class="text-2xl font-bold tracking-tight text-white">PAPAIWAT</h1>
            <p class="mt-1 text-sm text-slate-400">ระบบจัดการเว็บไซต์</p>
        </div>

        <div class="shrink-0 border-b border-white/10 px-4 py-5">
            <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-4 shadow-lg shadow-slate-950/20 backdrop-blur">
                <p class="text-sm font-semibold text-white">{{ $admin?->username ?? '-' }}</p>
                <p class="mt-1 text-sm text-slate-400">{{ $admin?->role?->name ?? 'ไม่มีบทบาท' }}</p>
            </div>
        </div>

        <nav class="min-h-0 flex-1 space-y-3 overflow-y-auto px-4 py-5 text-sm">
            @if ($canViewDashboard)
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard')
                        ? 'block rounded-2xl border border-blue-400/30 bg-blue-900/80 px-4 py-3 font-medium text-blue-300 shadow-md shadow-blue-950/30'
                        : 'block rounded-2xl px-4 py-3 font-medium text-slate-400 hover:bg-white/5 hover:text-white' }}"
                >
                    แดชบอร์ด
                </a>
            @endif

            @foreach ($sidebarGroups as $group)
                @continue(empty($group['items']))

                <details
                    x-data="{ open: {{ $group['active'] ? 'true' : 'false' }} }"
                    x-bind:open="open"
                    class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03] p-2 shadow-lg shadow-slate-950/20 backdrop-blur"
                >
                    <summary
                        @click.prevent="open = !open"
                        class="cursor-pointer list-none rounded-xl px-3 py-2.5 font-medium text-slate-300 hover:bg-white/5 hover:text-white"
                    >
                        <div class="flex items-center justify-between">
                            <span>{{ $group['label'] }}</span>
                            <span class="text-xs text-slate-500">{{ count($group['items']) }}</span>
                        </div>
                    </summary>

                    <div class="mt-2 space-y-2 transition-all duration-200">
                        @foreach ($group['items'] as $item)
                            @if (! empty($item['items']))
                                <details
                                    x-data="{ open: {{ $item['active'] ? 'true' : 'false' }} }"
                                    x-bind:open="open"
                                    class="overflow-hidden rounded-xl border border-white/10 bg-[#0f1727]/70 p-2"
                                >
                                    <summary
                                        @click.prevent="open = !open"
                                        class="cursor-pointer list-none rounded-lg px-3 py-2 font-medium text-slate-400 hover:bg-white/5 hover:text-white"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span>{{ $item['label'] }}</span>
                                            <span class="text-xs text-slate-500">{{ count($item['items']) }}</span>
                                        </div>
                                    </summary>

                                    <div class="mt-1 space-y-1">
                                        @foreach ($item['items'] as $child)
                                            <a
                                                href="{{ route($child['route']) }}"
                                                class="{{ request()->routeIs($child['active'])
                                                    ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                                    : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                            >
                                                {{ $child['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="{{ request()->routeIs($item['active'])
                                        ? 'block rounded-xl border border-blue-400/30 bg-blue-900/80 px-4 py-2.5 text-blue-300 shadow-md shadow-blue-950/30'
                                        : 'block rounded-xl px-4 py-2.5 text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                >
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </details>
            @endforeach
        </nav>

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
