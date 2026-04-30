<x-layouts.admin title="แก้ไขผู้ใช้งาน">
    <div class="space-y-5 text-white">

        {{-- Header --}}
        <div class="rounded-3xl border border-white/10 bg-[#0f1424] px-6 py-6 shadow-lg shadow-slate-950/20">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.28em] text-blue-300">
                        ACCESS MANAGEMENT
                    </p>
                    <h1 class="text-2xl font-bold text-white">แก้ไขผู้ใช้งาน</h1>
                    <p class="mt-2 text-sm text-slate-400">อัปเดตข้อมูลผู้ดูแลระบบ</p>
                </div>

                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                >
                    กลับ
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $admin) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Account Info --}}
            <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                <h2 class="text-base font-semibold text-white">ข้อมูลบัญชี</h2>
                <p class="mt-1 text-sm text-slate-400">แก้ไขข้อมูลพื้นฐานของผู้ใช้</p>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-400">ชื่อผู้ใช้</label>
                        <input
                            type="text"
                            name="username"
                            value="{{ old('username', $admin->username) }}"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('username')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-400">อีเมล</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $admin->email) }}"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-400">บทบาท</label>
                        <select
                            name="role_id"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option class="bg-slate-900" value="">เลือกบทบาท</option>
                            @foreach ($roles as $role)
                                <option class="bg-slate-900" value="{{ $role->id }}" @selected(old('role_id', $admin->role_id) == $role->id)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-400">เบอร์โทร</label>
                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $admin->phone) }}"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                {{-- Status --}}
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                    <h2 class="text-base font-semibold text-white">สถานะบัญชี</h2>
                    <p class="mt-1 text-sm text-slate-400">กำหนดสถานะการเข้าใช้งานของบัญชีนี้</p>

                    <div class="mt-5">
                        <label class="mb-1.5 block text-sm font-medium text-slate-400">สถานะ</label>
                        <select
                            name="status"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option class="bg-slate-900" value="active" @selected(old('status', $admin->status) === 'active')>
                                ใช้งาน
                            </option>
                            <option class="bg-slate-900" value="inactive" @selected(old('status', $admin->status) === 'inactive')>
                                ไม่ใช้งาน
                            </option>
                        </select>

                        @error('status')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg shadow-slate-950/20 backdrop-blur">
                    <h2 class="text-base font-semibold text-white">เปลี่ยนรหัสผ่าน</h2>
                    <p class="mt-1 text-sm text-slate-400">เว้นว่างไว้หากไม่ต้องการเปลี่ยน</p>

                    <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-400">รหัสผ่านใหม่</label>
                            <input
                                type="password"
                                name="password"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-400">ยืนยันรหัสผ่าน</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            >
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center rounded-2xl border border-white/10 px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                >
                    ยกเลิก
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                >
                    บันทึก
                </button>
            </div>
        </form>

    </div>
</x-layouts.admin>