<x-layouts.admin title="โปรไฟล์ของฉัน" header="โปรไฟล์ของฉัน">
    <div class="space-y-6 text-white">
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="border-b border-white/10 bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950 px-6 py-6">
                <p class="text-sm font-medium text-blue-300">My Profile</p>
                <h1 class="mt-1 text-2xl font-bold text-white">โปรไฟล์ของฉัน</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-400">
                    แก้ไขข้อมูลบัญชีและเปลี่ยนรหัสผ่านของตัวเองได้โดยไม่ต้องมีสิทธิ์จัดการผู้ใช้งาน
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
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

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                <h2 class="text-base font-semibold text-white">ข้อมูลบัญชี</h2>
                <p class="mt-1 text-sm text-slate-400">ข้อมูลนี้ใช้แสดงในระบบผู้ดูแล</p>

                <form method="POST" action="{{ route('admin.profile.update') }}" class="mt-5 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="username" class="mb-1.5 block text-sm font-medium text-slate-400">ชื่อผู้ใช้</label>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username', $admin->username) }}"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('username')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-400">อีเมล</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $admin->email) }}"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-400">เบอร์โทร</label>
                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone', $admin->phone) }}"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                        >
                            บันทึกโปรไฟล์
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                <h2 class="text-base font-semibold text-white">เปลี่ยนรหัสผ่าน</h2>
                <p class="mt-1 text-sm text-slate-400">
                    ต้องกรอกรหัสผ่านปัจจุบันก่อนเปลี่ยน รหัสใหม่ต้องยาวอย่างน้อย 12 ตัวอักษรและมีตัวพิมพ์เล็ก ตัวพิมพ์ใหญ่ และตัวเลข
                </p>

                <form method="POST" action="{{ route('admin.profile.password.update') }}" class="mt-5 space-y-5" x-data="{ showPassword: false }">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="mb-1.5 block text-sm font-medium text-slate-400">รหัสผ่านปัจจุบัน</label>
                        <div class="relative">
                            <input
                                id="current_password"
                                :type="showPassword ? 'text' : 'password'"
                                name="current_password"
                                autocomplete="current-password"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 pr-16 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-1 right-1 rounded-xl border border-white/10 px-3 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white"
                                x-text="showPassword ? 'ซ่อน' : 'ดู'"
                            ></button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-400">รหัสผ่านใหม่</label>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                autocomplete="new-password"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 pr-16 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-1 right-1 rounded-xl border border-white/10 px-3 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white"
                                x-text="showPassword ? 'ซ่อน' : 'ดู'"
                            ></button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-400">ยืนยันรหัสผ่านใหม่</label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                name="password_confirmation"
                                autocomplete="new-password"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 pr-16 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-1 right-1 rounded-xl border border-white/10 px-3 text-xs font-medium text-slate-300 hover:bg-white/10 hover:text-white"
                                x-text="showPassword ? 'ซ่อน' : 'ดู'"
                            ></button>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 px-4 py-3 text-xs leading-5 text-blue-200">
                        หลังเปลี่ยนรหัสผ่าน ระบบจะยกเลิก session อื่นของบัญชีนี้ เหลือเฉพาะ session ปัจจุบัน
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                        >
                            เปลี่ยนรหัสผ่าน
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.admin>
