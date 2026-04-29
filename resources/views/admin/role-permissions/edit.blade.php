<x-layouts.admin title="จัดการสิทธิ์บทบาท">
    @php
        $initialSelectedPermissionIds = $role->permissions
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        $permissionGroups = $permissions
            ->map(fn ($items) => $items
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
            )
            ->toArray();

        $permissionIndex = $permissions
            ->map(fn ($items) => $items
                ->map(fn ($permission) => [
                    'id' => (int) $permission->id,
                    'name' => $permission->name,
                    'key' => $permission->key,
                ])
                ->values()
            )
            ->toArray();

        $totalPermissionCount = $permissions->flatten()->count();
    @endphp

    <div
        class="text-white"
        x-data="permissionManager(
            @js($initialSelectedPermissionIds),
            @js($permissionGroups),
            @js($permissionIndex)
        )"
    >

        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <div class="mb-3 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                    <a href="{{ route('admin.roles.index') }}" class="transition hover:text-slate-300">
                        บทบาท
                    </a>
                    <span>›</span>
                    <span class="rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-0.5 text-xs font-medium text-blue-300">
                        {{ $role->name }}
                    </span>
                    <span>›</span>
                    <span class="text-slate-400">จัดการสิทธิ์</span>
                </div>

                <h1 class="text-2xl font-semibold text-white">จัดการสิทธิ์บทบาท</h1>
                <p class="mt-1 text-sm text-slate-500">
                    กำหนดสิทธิ์การใช้งานสำหรับบทบาท
                    <span class="font-medium text-slate-300">{{ $role->name }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.roles.index') }}"
                    class="inline-flex items-center rounded-lg border border-white/10 bg-white/[0.04] px-4 py-2 text-sm text-slate-300 transition hover:bg-white/10"
                >
                    ยกเลิก
                </a>

                <button
                    form="permissions-form"
                    type="submit"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                >
                    บันทึกสิทธิ์
                </button>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[240px_1fr]">

            {{-- Sidebar --}}
            <aside class="space-y-4">
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                    <p class="mb-1 text-xs uppercase tracking-widest text-slate-500">เลือกแล้ว</p>

                    <p class="text-4xl font-bold text-blue-400" x-text="selectedCount"></p>

                    <p class="mt-1 text-xs text-slate-500">
                        จากทั้งหมด {{ $totalPermissionCount }} สิทธิ์
                    </p>

                    <div class="mt-3 h-1 w-full overflow-hidden rounded-full bg-white/10">
                        <div
                            class="h-1 rounded-full bg-blue-500 transition-all duration-300"
                            :style="`width: ${progressPercent}%`"
                        ></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                    <p class="mb-3 text-xs uppercase tracking-widest text-slate-500">ภาพรวมกลุ่ม</p>

                    <div class="flex flex-col gap-2.5">
                        @foreach ($permissions as $groupKey => $groupPermissions)
                            <div class="flex items-center justify-between gap-3">
                                <span class="truncate text-xs text-slate-400">
                                    {{ ucfirst($groupKey) }}
                                </span>

                                <span
                                    class="shrink-0 rounded-full border px-2 py-0.5 text-[10px] font-medium"
                                    :class="groupSelectedCount('{{ $groupKey }}') === {{ $groupPermissions->count() }}
                                        ? 'border-emerald-400/25 bg-emerald-500/10 text-emerald-400'
                                        : groupSelectedCount('{{ $groupKey }}') > 0
                                            ? 'border-blue-400/25 bg-blue-500/10 text-blue-400'
                                            : 'border-white/10 bg-white/[0.04] text-slate-500'"
                                    x-text="`${groupSelectedCount('{{ $groupKey }}')}/{{ $groupPermissions->count() }}`"
                                ></span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl border border-amber-400/20 bg-amber-500/[0.07] p-3.5 text-xs leading-relaxed text-amber-500/90">
                    ตรวจสอบสิทธิ์ให้ถูกต้อง เพราะมีผลต่อการเข้าถึงเมนูและการใช้งานของผู้ดูแลระบบทันที
                </div>
            </aside>

            {{-- Main --}}
            <form
                id="permissions-form"
                method="POST"
                action="{{ route('admin.roles.permissions.update', $role) }}"
                class="min-w-0"
            >
                @csrf
                @method('PUT')

                {{-- Search --}}
                <div class="mb-4 flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5">
                    <svg class="h-4 w-4 shrink-0 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>

                    <input
                        type="text"
                        placeholder="ค้นหาสิทธิ์..."
                        x-model.debounce.150ms="search"
                        class="flex-1 bg-transparent text-sm text-slate-300 placeholder:text-slate-600 focus:outline-none"
                    >
                </div>

                {{-- Permission Groups --}}
                <div class="flex flex-col gap-3">
                    @foreach ($permissions as $groupKey => $groupPermissions)
                        @php
                            $selected = $groupPermissions
                                ->filter(fn ($p) => $role->permissions->contains('id', $p->id))
                                ->count();
                        @endphp

                        <details
                            data-group="{{ $groupKey }}"
                            class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03]"
                            x-show="groupVisible('{{ $groupKey }}')"
                        >
                            {{-- Group Header --}}
                            <summary class="cursor-pointer list-none px-5 py-3.5 transition hover:bg-white/[0.04]">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <svg
                                            class="h-3.5 w-3.5 shrink-0 text-slate-500 transition-transform duration-200 group-open:rotate-90"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>

                                        <span class="truncate text-sm font-medium text-slate-200">
                                            {{ ucfirst($groupKey) }}
                                        </span>

                                        <span
                                            class="shrink-0 rounded-full border px-2 py-0.5 text-[10px] font-medium"
                                            :class="groupSelectedCount('{{ $groupKey }}') === {{ $groupPermissions->count() }}
                                                ? 'border-emerald-400/25 bg-emerald-500/10 text-emerald-400'
                                                : groupSelectedCount('{{ $groupKey }}') > 0
                                                    ? 'border-blue-400/25 bg-blue-500/10 text-blue-400'
                                                    : 'border-white/10 bg-white/[0.04] text-slate-500'"
                                            x-text="`${groupSelectedCount('{{ $groupKey }}')}/{{ $groupPermissions->count() }}`"
                                        ></span>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-1" @click.stop>
                                        <button
                                            type="button"
                                            class="rounded-md px-2.5 py-1 text-xs text-blue-400 transition hover:bg-blue-500/10"
                                            @click="selectGroup('{{ $groupKey }}', true)"
                                        >
                                            เลือกทั้งหมด
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-md px-2.5 py-1 text-xs text-slate-500 transition hover:bg-white/[0.06]"
                                            @click="selectGroup('{{ $groupKey }}', false)"
                                        >
                                            ล้าง
                                        </button>
                                    </div>
                                </div>
                            </summary>

                            {{-- Permissions Grid --}}
                            <div class="border-t border-white/10">
                                <div class="grid grid-cols-1 md:grid-cols-2">
                                    @foreach ($groupPermissions as $permission)
                                        <label
                                            class="flex cursor-pointer items-start gap-3 px-5 py-3.5 transition hover:bg-white/[0.04] md:odd:border-r md:border-white/[0.06]"
                                            x-show="permVisible({{ $permission->id }})"
                                        >
                                            <input
                                                type="checkbox"
                                                name="permission_ids[]"
                                                value="{{ $permission->id }}"
                                                x-model.number="selected"
                                                class="mt-0.5 h-4 w-4 shrink-0 rounded border-white/20 bg-slate-900 text-blue-500 focus:ring-1 focus:ring-blue-500 focus:ring-offset-0"
                                            >

                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-slate-200">
                                                    {{ $permission->name }}
                                                </p>

                                                <code class="mt-1 inline-block max-w-full truncate rounded bg-slate-950/60 px-1.5 py-0.5 text-[10px] text-slate-500">
                                                    {{ $permission->key }}
                                                </code>

                                                @if ($permission->description)
                                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
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
            </form>
        </div>
    </div>

    <script>
        function permissionManager(initialSelected, groups, permissionIndex) {
            return {
                search: '',
                selected: initialSelected.map((id) => Number(id)),

                get selectedCount() {
                    return this.selected.length;
                },

                get totalCount() {
                    return Object.values(groups).flat().length;
                },

                get progressPercent() {
                    if (this.totalCount === 0) {
                        return 0;
                    }

                    return Math.round((this.selectedCount / this.totalCount) * 100);
                },

                groupSelectedCount(groupKey) {
                    return groups[groupKey].filter((permissionId) => {
                        return this.selected.includes(Number(permissionId));
                    }).length;
                },

                selectGroup(groupKey, value) {
                    const ids = groups[groupKey].map((id) => Number(id));

                    if (value) {
                        this.selected = Array.from(new Set([
                            ...this.selected.map((id) => Number(id)),
                            ...ids,
                        ]));
                        return;
                    }

                    this.selected = this.selected
                        .map((id) => Number(id))
                        .filter((id) => ! ids.includes(id));
                },

                permVisible(permissionId) {
                    if (! this.search) {
                        return true;
                    }

                    const keyword = this.search.toLowerCase();
                    const permissions = Object.values(permissionIndex).flat();

                    const permission = permissions.find((item) => {
                        return Number(item.id) === Number(permissionId);
                    });

                    if (! permission) {
                        return false;
                    }

                    return permission.name.toLowerCase().includes(keyword)
                        || permission.key.toLowerCase().includes(keyword);
                },

                groupVisible(groupKey) {
                    if (! this.search) {
                        return true;
                    }

                    const keyword = this.search.toLowerCase();

                    return permissionIndex[groupKey].some((permission) => {
                        return permission.name.toLowerCase().includes(keyword)
                            || permission.key.toLowerCase().includes(keyword);
                    });
                },
            };
        }
    </script>
</x-layouts.admin>