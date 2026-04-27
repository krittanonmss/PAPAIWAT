<x-layouts.admin title="สร้างผู้ใช้งาน">
    <div class="mx-auto max-w-5xl space-y-6 text-white">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">สร้างผู้ใช้งาน</h1>
                <p class="text-sm text-slate-400">เพิ่มบัญชีผู้ดูแลระบบใหม่</p>
            </div>

            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-white/5"
            >
                กลับ
            </a>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                {{-- Account --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6 backdrop-blur">
                    <h2 class="text-base font-semibold text-white">ข้อมูลบัญชี</h2>
                    <p class="mt-1 text-sm text-slate-400">กรอกข้อมูลผู้ใช้งานใหม่</p>

                    <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-400">ชื่อผู้ใช้</label>
                            <input
                                type="text"
                                name="username"
                                class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                            >
                            @error('username')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-400">อีเมล</label>
                            <input
                                type="email"
                                name="email"
                                class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-400">บทบาท</label>
                            <select
                                name="role_id"
                                class="w-full appearance-none rounded-xl border border-white/10 bg-slate-900 px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                            >
                                <option class="bg-slate-900 text-white" value="">เลือกบทบาท</option>
                                @foreach ($roles as $role)
                                    <option class="bg-slate-900 text-white" value="{{ $role->id }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-400">เบอร์โทร</label>
                            <input
                                type="text"
                                name="phone"
                                class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6 backdrop-blur">
                    <h2 class="text-base font-semibold text-white">สถานะบัญชี</h2>

                    <div class="mt-5">
                        <select
                            name="status"
                            class="w-full appearance-none rounded-xl border border-white/10 bg-slate-900 px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                        >
                            <option class="bg-slate-900 text-white" value="">เลือกสถานะ</option>
                            <option class="bg-slate-900 text-white" value="active">ใช้งาน</option>
                            <option class="bg-slate-900 text-white" value="inactive">ไม่ใช้งาน</option>
                        </select>

                        @error('status')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-6 backdrop-blur">
                    <h2 class="text-base font-semibold text-white">ตั้งรหัสผ่าน</h2>

                    <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-400">รหัสผ่าน</label>
                            <input
                                type="password"
                                name="password"
                                class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-400">ยืนยันรหัสผ่าน</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="w-full rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500"
                    >
                        สร้างผู้ใช้งาน
                    </button>

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center rounded-xl border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-white/5"
                    >
                        ยกเลิก
                    </a>
                </div>

            </form>
        </div>

    </div>
</x-layouts.admin>