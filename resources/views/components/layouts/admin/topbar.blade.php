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

        <div class="flex items-center gap-2">
            @if ($adminInAppNotifications)
                <details class="relative">
                    <summary class="flex h-10 cursor-pointer list-none items-center gap-2 rounded-xl border border-white/10 bg-white/[0.04] px-3 text-sm text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <span>แจ้งเตือน</span>
                        @if ($adminUnreadNotifications->isNotEmpty())
                            <span class="rounded-full bg-blue-600 px-2 py-0.5 text-xs font-semibold text-white">{{ $adminUnreadNotifications->count() }}</span>
                        @endif
                    </summary>

                    <div class="absolute right-0 mt-2 w-80 overflow-hidden rounded-2xl border border-white/10 bg-slate-950 shadow-2xl shadow-slate-950/40">
                        @forelse ($adminUnreadNotifications as $notification)
                            <div class="border-b border-white/10 px-4 py-3 last:border-b-0">
                                <p class="text-sm font-semibold text-white">{{ $notification->title }}</p>
                                <p class="mt-1 line-clamp-2 text-xs text-slate-400">{{ $notification->message }}</p>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-sm text-slate-400">ไม่มีแจ้งเตือนใหม่</div>
                        @endforelse
                    </div>
                </details>
            @endif

            <a
                href="{{ route('admin.profile.edit') }}"
                class="hidden rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-sm text-slate-300 transition hover:bg-white/10 hover:text-white sm:inline-flex"
            >
                {{ $admin?->email ?? '-' }}
            </a>
        </div>
    </div>
</header>
