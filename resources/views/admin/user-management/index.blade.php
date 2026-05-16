<x-layouts.admin title="จัดการผู้ใช้งาน">
    @php
        $currentAdminId = auth('admin')->id();
    @endphp

    <div class="space-y-5 text-white">

        {{-- Header + Stats --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="grid gap-5 xl:grid-cols-[1.2fr_1fr] xl:items-center">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between xl:block">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                            Access Management
                        </p>
                        <h1 class="text-2xl font-bold text-white">จัดการผู้ใช้งาน</h1>
                        <p class="mt-1 text-sm text-slate-400">
                            จัดการผู้ดูแลระบบ บทบาท และสถานะการเข้าใช้งาน
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.users.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                    >
                        <span class="text-lg leading-none">+</span>
                        เพิ่มผู้ใช้ใหม่
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 xl:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                        <p class="text-xs text-slate-400">ทั้งหมด</p>
                        <p class="mt-1 text-2xl font-bold text-white">{{ $admins->total() }}</p>
                    </div>

                    <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4">
                        <p class="text-xs text-emerald-300">ใช้งานอยู่</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-300">
                            {{ $admins->where('status', 'active')->count() }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-red-400/20 bg-red-500/10 p-4">
                        <p class="text-xs text-red-300">ไม่ใช้งาน</p>
                        <p class="mt-1 text-2xl font-bold text-red-300">
                            {{ $admins->where('status', 'inactive')->count() }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-yellow-400/20 bg-yellow-500/10 p-4">
                        <p class="text-xs text-yellow-300">ผู้ดูแลสูงสุด</p>
                        <p class="mt-1 text-2xl font-bold text-yellow-300">
                            {{ $admins->filter(fn ($a) => $a->role?->name === 'ผู้ดูแลสูงสุด')->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 px-5 py-3 text-sm text-red-300">
                {{ session('error') }}
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

        {{-- Filter --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหาผู้ใช้</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="ชื่อผู้ใช้ / อีเมล / เบอร์โทร"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                </div>

                <div class="lg:col-span-3">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">บทบาท</label>
                    <select
                        name="role_id"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ทั้งหมด</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" class="bg-slate-900" @selected((string) request('role_id') === (string) $role->id)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                    <select
                        name="status"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="" class="bg-slate-900">ทั้งหมด</option>
                        <option value="active" class="bg-slate-900" @selected(request('status') === 'active')>ใช้งานอยู่</option>
                        <option value="inactive" class="bg-slate-900" @selected(request('status') === 'inactive')>ไม่ใช้งาน</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                    <button
                        type="submit"
                        class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        ค้นหา
                    </button>

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5"
                    >
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        {{-- Bulk การจัดการs --}}
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">จัดการหลายผู้ใช้พร้อมกัน</h2>
                    <p class="text-sm text-slate-400">เลือกผู้ใช้จากตาราง แล้วจัดการแบบกลุ่ม</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                <form
                    method="POST"
                    action="{{ route('admin.users.bulk-role') }}"
                    id="bulk-role-form"
                    class="rounded-2xl border border-white/10 bg-slate-950/30 p-3"
                    data-bulk-form
                    data-confirm-message="ยืนยันการเปลี่ยนบทบาทผู้ใช้งานที่เลือก?"
                >
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <select
                            name="role_id"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            required
                        >
                            <option value="" class="bg-slate-900">เลือกบทบาท</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" class="bg-slate-900">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        <button
                            type="submit"
                            class="whitespace-nowrap rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            เปลี่ยนบทบาท
                        </button>
                    </div>
                </form>

                <form
                    method="POST"
                    action="{{ route('admin.users.bulk-status') }}"
                    id="bulk-status-form"
                    class="rounded-2xl border border-white/10 bg-slate-950/30 p-3"
                    data-bulk-form
                    data-confirm-message="ยืนยันการเปลี่ยนสถานะผู้ใช้งานที่เลือก?"
                >
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <select
                            name="status"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                            required
                        >
                            <option value="" class="bg-slate-900">เลือกสถานะ</option>
                            <option value="active" class="bg-slate-900">เปิดใช้งาน</option>
                            <option value="inactive" class="bg-slate-900">ปิดใช้งาน</option>
                        </select>

                        <button
                            type="submit"
                            class="whitespace-nowrap rounded-2xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700"
                        >
                            เปลี่ยนสถานะ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-white">รายการผู้ใช้งาน</h2>
                    <p class="text-sm text-slate-400">จัดการผู้ใช้รายบุคคล หรือเลือกหลายรายการเพื่อจัดการพร้อมกัน</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="w-20 px-4 py-3 text-center">
                                <input
                                    type="checkbox"
                                    id="select-all-admins"
                                    class="rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-blue-500"
                                    aria-label="เลือกผู้ใช้ทั้งหมดในหน้านี้"
                                >
                            </th>
                            <th class="px-4 py-3 text-left">ผู้ใช้</th>
                            <th class="px-4 py-3 text-left">บทบาท</th>
                            <th class="px-4 py-3 text-left">สถานะ</th>
                            <th class="px-4 py-3 text-left">เข้าสู่ระบบล่าสุด</th>
                            <th class="px-4 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($admins as $admin)
                            <tr class="transition hover:bg-white/[0.06]">
                                <td class="px-4 py-3 text-center">
                                    @if ($admin->id !== $currentAdminId)
                                        <input
                                            type="checkbox"
                                            value="{{ $admin->id }}"
                                            data-admin-checkbox
                                            class="rounded border-white/20 bg-slate-950/40 text-blue-600 focus:ring-blue-500"
                                            aria-label="เลือกผู้ใช้ {{ $admin->username }}"
                                        >
                                    @else
                                        <span class="whitespace-nowrap rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-xs text-slate-400">
                                            ตัวเอง
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white shadow-lg shadow-indigo-950/30">
                                            {{ strtoupper(substr($admin->username, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-white">{{ $admin->username }}</p>
                                            <p class="truncate text-xs text-slate-400">{{ $admin->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-200">
                                        {{ $admin->role?->name ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($admin->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                            ใช้งานอยู่
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-400/20 bg-red-500/10 px-3 py-1 text-xs font-medium text-red-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-300"></span>
                                            ไม่ใช้งาน
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-slate-400">
                                    {{ $admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.users.show', $admin) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            ดู
                                        </a>

                                        <a
                                            href="{{ route('admin.users.edit', $admin) }}"
                                            class="rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-300 transition hover:bg-white/5 hover:text-white"
                                        >
                                            แก้ไข
                                        </a>

                                        @if ($admin->id !== $currentAdminId)
                                            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('ลบผู้ใช้นี้หรือไม่?')"
                                                    class="rounded-xl border border-red-400/20 px-3 py-1.5 text-xs font-medium text-red-300 transition hover:bg-red-500/10"
                                                >
                                                    ลบ
                                                </button>
                                            </form>
                                        @else
                                            <button
                                                type="button"
                                                disabled
                                                class="cursor-not-allowed rounded-xl border border-white/10 px-3 py-1.5 text-xs font-medium text-slate-600"
                                            >
                                                ลบ
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center">
                                    <p class="text-base font-medium text-slate-300">ไม่พบข้อมูลผู้ใช้งาน</p>
                                    <p class="mt-1 text-sm text-slate-500">ลองเปลี่ยนเงื่อนไขการค้นหา หรือเพิ่มผู้ใช้ใหม่</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-white/10 px-5 py-3">
                {{ $admins->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('select-all-admins');
            const checkboxes = Array.from(document.querySelectorAll('[data-admin-checkbox]'));
            const bulkForms = document.querySelectorAll('[data-bulk-form]');

            const clearInjectedInputs = (form) => {
                form.querySelectorAll('[data-injected-admin-id]').forEach((input) => input.remove());
            };

            const injectSelectedAdmins = (form, selectedIds) => {
                selectedIds.forEach((id) => {
                    const input = document.createElement('input');

                    input.type = 'hidden';
                    input.name = 'admin_ids[]';
                    input.value = id;
                    input.setAttribute('data-injected-admin-id', 'true');

                    form.appendChild(input);
                });
            };

            selectAll?.addEventListener('change', () => {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selectAll.checked;
                });
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    if (! selectAll) {
                        return;
                    }

                    selectAll.checked = checkboxes.length > 0 && checkboxes.every((item) => item.checked);
                });
            });

            bulkForms.forEach((form) => {
                form.addEventListener('submit', (event) => {
                    clearInjectedInputs(form);

                    const selectedIds = checkboxes
                        .filter((checkbox) => checkbox.checked)
                        .map((checkbox) => checkbox.value);

                    if (selectedIds.length === 0) {
                        event.preventเริ่มต้น();
                        alert('กรุณาเลือกผู้ใช้งานอย่างน้อย 1 คน');
                        return;
                    }

                    const confirmMessage = form.dataset.confirmMessage || 'ยืนยันการทำรายการ?';

                    if (! confirm(confirmMessage)) {
                        event.preventเริ่มต้น();
                        return;
                    }

                    injectSelectedAdmins(form, selectedIds);
                });
            });
        });
    </script>
</x-layouts.admin>