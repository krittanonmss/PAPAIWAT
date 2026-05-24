<x-layouts.admin title="โปรไฟล์ของฉัน" header="โปรไฟล์ของฉัน">
    @php
        $initial = mb_strtoupper(mb_substr($admin->username, 0, 1));
        $roleName = $admin->role?->name ?? 'ยังไม่ได้กำหนดบทบาท';
        $lastLoginLabel = $admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : '-';
        $createdLabel = $admin->created_at ? $admin->created_at->format('d/m/Y H:i') : '-';
        $updatedLabel = $admin->updated_at ? $admin->updated_at->format('d/m/Y H:i') : '-';
        $isActive = $admin->status === 'active';
        $profileErrors = $errors->getBag('profile');
        $passwordErrors = $errors->getBag('password');
        $hasPasswordErrors = $passwordErrors->any();
    @endphp

    <div class="space-y-5 text-white">
        <div class="px-6 py-6">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 text-2xl font-bold text-white shadow-lg shadow-blue-950/30">
                        {{ $initial }}
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-300">MY ACCOUNT</p>
                        <h1 class="mt-2 truncate text-2xl font-bold text-white">{{ $admin->username }}</h1>
                        <p class="mt-1 break-all text-sm text-slate-300">{{ $admin->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:justify-end">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3">
                        <p class="text-xs text-slate-400">บทบาท</p>
                        <p class="mt-1 max-w-40 truncate text-sm font-semibold text-white">{{ $roleName }}</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3">
                        <p class="text-xs text-slate-400">สถานะ</p>
                        <p class="mt-1 inline-flex items-center gap-1.5 text-sm font-semibold {{ $isActive ? 'text-emerald-300' : 'text-red-300' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-emerald-300' : 'bg-red-300' }}"></span>
                            {{ $isActive ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3">
                        <p class="text-xs text-slate-400">Session</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ number_format($activeSessionsCount) }} active</p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($profileErrors->any())
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-3 text-sm text-red-300">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($profileErrors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(360px,0.65fr)]">
            <div class="space-y-5">
                <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-white">ข้อมูลส่วนตัว</h2>
                            <p class="mt-1 text-sm text-slate-400">ข้อมูลนี้ใช้ระบุตัวตนของคุณในระบบผู้ดูแล</p>
                        </div>

                        <span class="inline-flex w-fit items-center rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">
                            อัปเดตล่าสุด {{ $updatedLabel }}
                        </span>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.profile.update') }}"
                        class="mt-6 space-y-5"
                        autocomplete="off"
                        x-data="{
                            username: @js($admin->username),
                            email: @js($admin->email),
                            phone: @js($admin->phone ?? ''),
                            resetProfileValues() {
                                this.username = @js($admin->username);
                                this.email = @js($admin->email);
                                this.phone = @js($admin->phone ?? '');
                            },
                        }"
                        x-init="$nextTick(() => { resetProfileValues(); setTimeout(() => resetProfileValues(), 250); })"
                    >
                        @csrf
                        @method('PUT')

                        <input type="text" name="profile_autofill_username" class="hidden" tabindex="-1" autocomplete="username">
                        <input type="password" name="profile_autofill_password" class="hidden" tabindex="-1" autocomplete="current-password">

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="username" class="mb-1.5 block text-sm font-medium text-slate-400">ชื่อผู้ใช้</label>
                                <input
                                    id="username"
                                    type="text"
                                    name="username"
                                    value="{{ $admin->username }}"
                                    x-model="username"
                                    autocomplete="off"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                @error('username', 'profile')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-400">อีเมล</label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ $admin->email }}"
                                    x-model="email"
                                    autocomplete="off"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                @error('email', 'profile')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-400">เบอร์โทร</label>
                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                value="{{ $admin->phone }}"
                                x-model="phone"
                                autocomplete="off"
                                placeholder="เช่น 0812345678"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                            @error('phone', 'profile')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-700"
                            >
                                บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </section>

                <section
                    class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur"
                    x-data="{ showPasswordForm: @js($hasPasswordErrors), showCurrentPassword: false, showNewPassword: false }"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-white">เปลี่ยนรหัสผ่าน</h2>
                            <p class="mt-1 text-sm text-slate-400">
                                ต้องยืนยันรหัสผ่านปัจจุบันก่อนเปลี่ยน และรหัสใหม่ต้องยาวอย่างน้อย 12 ตัวอักษร มีตัวพิมพ์เล็ก ตัวพิมพ์ใหญ่ และตัวเลข
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="showPasswordForm = !showPasswordForm"
                            :aria-expanded="showPasswordForm.toString()"
                            class="inline-flex shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                            x-text="showPasswordForm ? 'ซ่อน' : 'เปลี่ยนรหัสผ่าน'"
                        ></button>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.profile.password.update') }}"
                        class="mt-6 space-y-5"
                        x-show="showPasswordForm"
                        x-cloak
                    >
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="mb-1.5 block text-sm font-medium text-slate-400">รหัสผ่านปัจจุบัน</label>
                            <div class="relative">
                                <input
                                    id="current_password"
                                    :type="showCurrentPassword ? 'text' : 'password'"
                                    name="current_password"
                                    autocomplete="current-password"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 pr-16 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                <button
                                    type="button"
                                    @click="showCurrentPassword = !showCurrentPassword"
                                    class="absolute inset-y-1 right-1 rounded-xl border border-white/10 px-3 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white"
                                    x-text="showCurrentPassword ? 'ซ่อน' : 'ดู'"
                                ></button>
                            </div>
                            @error('current_password', 'password')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-400">รหัสผ่านใหม่</label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        :type="showNewPassword ? 'text' : 'password'"
                                        name="password"
                                        autocomplete="new-password"
                                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 pr-16 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                    >
                                    <button
                                        type="button"
                                        @click="showNewPassword = !showNewPassword"
                                        class="absolute inset-y-1 right-1 rounded-xl border border-white/10 px-3 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white"
                                        x-text="showNewPassword ? 'ซ่อน' : 'ดู'"
                                    ></button>
                                </div>
                                @error('password', 'password')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-400">ยืนยันรหัสผ่านใหม่</label>
                                <div class="relative">
                                    <input
                                        id="password_confirmation"
                                        :type="showNewPassword ? 'text' : 'password'"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 pr-16 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                    >
                                    <button
                                        type="button"
                                        @click="showNewPassword = !showNewPassword"
                                        class="absolute inset-y-1 right-1 rounded-xl border border-white/10 px-3 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white"
                                        x-text="showNewPassword ? 'ซ่อน' : 'ดู'"
                                    ></button>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 rounded-2xl border border-blue-400/20 bg-blue-500/10 px-4 py-3 text-xs leading-5 text-blue-100 sm:flex-row sm:items-center sm:justify-between">
                            <span>หลังเปลี่ยนรหัสผ่าน ระบบจะยกเลิก session อื่นของบัญชีนี้ เหลือเฉพาะ session ปัจจุบัน</span>
                            <span class="shrink-0 rounded-full bg-blue-400/15 px-3 py-1 font-medium text-blue-100">Security update</span>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-700"
                            >
                                เปลี่ยนรหัสผ่าน
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <aside class="space-y-5">
                <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                    <h2 class="text-base font-semibold text-white">ข้อมูลบัญชี</h2>
                    <p class="mt-1 text-sm text-slate-400">สถานะและประวัติการใช้งานของบัญชีนี้</p>

                    <div class="mt-5 divide-y divide-white/10">
                        <div class="grid grid-cols-2 gap-3 py-3">
                            <span class="text-sm text-slate-400">บทบาท</span>
                            <span class="min-w-0 truncate text-right text-sm text-white">{{ $roleName }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 py-3">
                            <span class="text-sm text-slate-400">สถานะ</span>
                            <span class="text-right text-sm {{ $isActive ? 'text-emerald-300' : 'text-red-300' }}">{{ $isActive ? 'ใช้งาน' : 'ไม่ใช้งาน' }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 py-3">
                            <span class="text-sm text-slate-400">เข้าสู่ระบบล่าสุด</span>
                            <span class="text-right text-sm text-slate-200">{{ $lastLoginLabel }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 py-3">
                            <span class="text-sm text-slate-400">สร้างบัญชีเมื่อ</span>
                            <span class="text-right text-sm text-slate-200">{{ $createdLabel }}</span>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-white">Session ที่ใช้งานอยู่</h2>
                            <p class="mt-1 text-sm text-slate-400">แสดงสูงสุด 4 session ล่าสุด</p>
                        </div>

                        <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-slate-300">
                            {{ number_format($activeSessionsCount) }}
                        </span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($activeSessions as $session)
                            @php
                                $isCurrentSession = hash_equals($currentSessionHash, $session->session_token_hash);
                            @endphp

                            <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="truncate text-sm font-semibold text-white">
                                        {{ $session->ip_address ?: '-' }}
                                    </p>

                                    @if ($isCurrentSession)
                                        <span class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-xs text-emerald-300">
                                            ปัจจุบัน
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-400">
                                    {{ $session->user_agent ?: '-' }}
                                </p>

                                <div class="mt-3 grid grid-cols-2 gap-3 text-xs text-slate-400">
                                    <span>ล่าสุด {{ $session->last_seen_at ? $session->last_seen_at->format('d/m/Y H:i') : '-' }}</span>
                                    <span class="text-right">หมดอายุ {{ $session->expires_at ? $session->expires_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-white/10 bg-slate-950/30 px-4 py-5 text-sm text-slate-400">
                                ยังไม่มีข้อมูล session ที่ใช้งานอยู่
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-layouts.admin>
