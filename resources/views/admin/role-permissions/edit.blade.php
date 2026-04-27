<x-layouts.admin title="จัดการสิทธิ์บทบาท">
    <div class="mx-auto max-w-5xl space-y-6 text-white">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">จัดการสิทธิ์บทบาท</h1>
                <p class="mt-1 text-sm text-slate-400">
                    กำหนดสิทธิ์การใช้งานให้บทบาท:
                    <span class="font-medium text-blue-300">{{ $role->name }}</span>
                </p>
            </div>

            <a
                href="{{ route('admin.roles.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2 text-sm font-medium text-slate-200 hover:bg-white/10"
            >
                กลับไปรายการบทบาท
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300 backdrop-blur">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">กลุ่มสิทธิ์การใช้งาน</h2>
                        <p class="text-sm text-slate-400">กดเปิดแต่ละกลุ่มเพื่อเลือกสิทธิ์ที่ต้องการ</p>
                    </div>

                    <div class="text-sm text-slate-400">
                        เลือกแล้ว
                        <span class="font-semibold text-blue-300">
                            {{ $role->permissions->count() }}
                        </span>
                        สิทธิ์
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($permissions as $groupKey => $groupPermissions)
                    @php
                        $selectedCount = $groupPermissions->filter(function ($permission) use ($role) {
                            return $role->permissions->contains('id', $permission->id);
                        })->count();
                    @endphp

                    <details
                        class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/20 backdrop-blur"
                        {{ $selectedCount > 0 ? 'open' : '' }}
                    >
                        <summary class="cursor-pointer list-none border-b border-white/10 bg-white/[0.03] px-5 py-4 hover:bg-white/[0.06]">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-slate-950/40 text-slate-300">
                                        <svg
                                            class="h-4 w-4 transition-transform group-open:rotate-90"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.8"
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-base font-semibold text-white">
                                            {{ ucfirst($groupKey) }}
                                        </h3>
                                        <p class="mt-1 text-xs text-slate-400">
                                            เลือกแล้ว {{ $selectedCount }} จาก {{ $groupPermissions->count() }} สิทธิ์
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                                        {{ $selectedCount }}/{{ $groupPermissions->count() }}
                                    </span>
                                </div>
                            </div>
                        </summary>

                        <div
                            x-data
                            class="border-t border-white/10"
                        >
                            <div class="flex flex-col gap-3 border-b border-white/10 bg-slate-950/20 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-slate-400">
                                    เลือกเฉพาะสิทธิ์ที่บทบาทนี้จำเป็นต้องใช้
                                </p>

                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="rounded-lg border border-blue-400/20 bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-300 hover:bg-blue-500/20"
                                        @click="
                                            $el.closest('details')
                                                .querySelectorAll('input[type=checkbox]')
                                                .forEach(checkbox => checkbox.checked = true)
                                        "
                                    >
                                        เลือกทั้งกลุ่ม
                                    </button>

                                    <button
                                        type="button"
                                        class="rounded-lg border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs font-medium text-slate-300 hover:bg-white/10"
                                        @click="
                                            $el.closest('details')
                                                .querySelectorAll('input[type=checkbox]')
                                                .forEach(checkbox => checkbox.checked = false)
                                        "
                                    >
                                        ล้างกลุ่มนี้
                                    </button>
                                </div>
                            </div>

                            <div class="divide-y divide-white/10">
                                @foreach ($groupPermissions as $permission)
                                    <label class="flex cursor-pointer items-start gap-4 px-5 py-4 transition hover:bg-white/[0.05]">
                                        <input
                                            type="checkbox"
                                            name="permission_ids[]"
                                            value="{{ $permission->id }}"
                                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-900 text-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0"
                                            {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                                        >

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                                <p class="text-sm font-medium text-white">
                                                    {{ $permission->name }}
                                                </p>

                                                <code class="w-fit rounded-lg border border-white/10 bg-slate-950/50 px-2 py-1 text-xs text-slate-400">
                                                    {{ $permission->key }}
                                                </code>
                                            </div>

                                            @if ($permission->description)
                                                <p class="mt-2 text-xs leading-5 text-slate-400">
                                                    {{ $permission->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>

            <div class="sticky bottom-4 z-10 rounded-2xl border border-white/10 bg-slate-950/80 p-4 shadow-2xl shadow-slate-950/40 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-400">
                        ตรวจสอบสิทธิ์ให้ถูกต้องก่อนบันทึก เพราะมีผลต่อการเข้าถึงเมนูและการใช้งานของผู้ดูแล
                    </p>

                    <div class="flex items-center gap-3">
                        <a
                            href="{{ route('admin.roles.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2 text-sm font-medium text-slate-200 hover:bg-white/10"
                        >
                            ยกเลิก
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-2 text-sm font-medium text-white hover:opacity-90"
                        >
                            บันทึกสิทธิ์
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>