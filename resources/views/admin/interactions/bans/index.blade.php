<x-layouts.admin title="Bans" header="Bans">
    @php
        $filterSelectClass = 'w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
        $filterKeys = ['search', 'ban_type', 'status', 'date_from', 'date_to', 'per_page'];
        $activeFilterCount = collect(request()->only($filterKeys))
            ->reject(fn ($value, $key) => $key === 'per_page' && (string) $value === '20')
            ->filter(fn ($value) => filled($value))
            ->count();
    @endphp

    <div class="space-y-6 text-white">
        <section class="p-6">
            <p class="text-sm text-blue-300">การตรวจสอบ</p>
            <h1 class="mt-1 text-2xl font-bold">รายการบล็อก</h1>
            <p class="mt-2 text-sm text-slate-400">จัดการ visitor ที่ถูก block จากระบบ interaction</p>
        </section>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
        @endif

        <section class="relative z-40 rounded-3xl border border-white/10 bg-white/[0.04] p-4 shadow-lg shadow-slate-950/20 backdrop-blur">
            <form method="GET" action="{{ route('admin.interactions.bans.index') }}" class="space-y-4">
                <div class="flex flex-col gap-2 border-b border-white/10 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">ตัวกรองรายการบล็อก</h2>
                        <p class="mt-0.5 text-xs text-slate-400">ค้นหาตามเหตุผล hash ประเภทบล็อก สถานะหมดอายุ และวันที่สร้างรายการ</p>
                    </div>

                    @if ($activeFilterCount > 0)
                        <span class="inline-flex w-fit rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-200">
                            ใช้ตัวกรอง {{ $activeFilterCount }} รายการ
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                    <div class="lg:col-span-3">
                        <label for="search" class="mb-1.5 block text-xs font-medium text-slate-400">ค้นหา</label>
                        <input id="search" type="text" name="search" value="{{ $filters['search'] ?? request('search') }}" placeholder="เหตุผล / hash / type" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="ban_type" class="mb-1.5 block text-xs font-medium text-slate-400">ประเภทบล็อก</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'ban_type',
                            'name' => 'ban_type',
                            'selected' => $filters['ban_type'] ?? request('ban_type'),
                            'emptyLabel' => 'ทุกประเภท',
                            'placeholder' => 'เลือกประเภท',
                            'searchPlaceholder' => 'ค้นหาประเภท...',
                            'inputClass' => $filterSelectClass,
                            'options' => $types->map(fn ($type) => [
                                'value' => $type,
                                'label' => $type,
                                'search' => $type,
                            ]),
                        ])
                    </div>

                    <div class="lg:col-span-2">
                        <label for="status" class="mb-1.5 block text-xs font-medium text-slate-400">สถานะ</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'status',
                            'name' => 'status',
                            'selected' => $filters['status'] ?? request('status'),
                            'emptyLabel' => 'ทุกสถานะ',
                            'placeholder' => 'เลือกสถานะ',
                            'searchPlaceholder' => 'ค้นหาสถานะ...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect([
                                ['value' => 'active', 'label' => 'ยังมีผล', 'search' => 'active ยังมีผล'],
                                ['value' => 'expired', 'label' => 'หมดอายุแล้ว', 'search' => 'expired หมดอายุแล้ว'],
                                ['value' => 'permanent', 'label' => 'ถาวร', 'search' => 'permanent ถาวร ไม่มีวันหมดอายุ'],
                                ['value' => 'temporary', 'label' => 'มีวันหมดอายุ', 'search' => 'temporary มีวันหมดอายุ'],
                            ]),
                        ])
                    </div>

                    <div class="lg:col-span-2">
                        <label for="date_from" class="mb-1.5 block text-xs font-medium text-slate-400">ตั้งแต่วันที่</label>
                        <input id="date_from" type="date" name="date_from" value="{{ $filters['date_from'] ?? request('date_from') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="date_to" class="mb-1.5 block text-xs font-medium text-slate-400">ถึงวันที่</label>
                        <input id="date_to" type="date" name="date_to" value="{{ $filters['date_to'] ?? request('date_to') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div class="lg:col-span-1">
                        <label for="per_page" class="mb-1.5 block text-xs font-medium text-slate-400">ต่อหน้า</label>
                        @include('admin.content.partials._searchable_select', [
                            'id' => 'per_page',
                            'name' => 'per_page',
                            'selected' => (string) ($filters['per_page'] ?? request('per_page', 20)),
                            'allowEmpty' => false,
                            'placeholder' => 'จำนวน',
                            'searchPlaceholder' => 'ค้นหา...',
                            'inputClass' => $filterSelectClass,
                            'options' => collect(\App\Services\Admin\AdminPreferenceService::PER_PAGE_OPTIONS)->map(fn ($pageSize) => [
                                'value' => (string) $pageSize,
                                'label' => (string) $pageSize,
                                'search' => $pageSize . ' รายการ',
                            ]),
                        ])
                    </div>

                    <div class="grid grid-cols-2 gap-2 lg:col-span-2 lg:self-end">
                        <button type="submit" class="rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">ค้นหา</button>
                        <a href="{{ route('admin.interactions.bans.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/5">ล้าง</a>
                    </div>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 text-left">Type</th>
                            <th class="px-5 py-3 text-left">Hash</th>
                            <th class="px-5 py-3 text-left">Reason</th>
                            <th class="px-5 py-3 text-left">Expires</th>
                            <th class="px-5 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($bans as $ban)
                            <tr>
                                <td class="px-5 py-4 text-slate-300">{{ $ban->ban_type }}</td>
                                <td class="px-5 py-4 text-xs text-slate-500">{{ $ban->value_hash }}</td>
                                <td class="px-5 py-4 text-slate-300">{{ $ban->reason ?: '-' }}</td>
                                <td class="px-5 py-4 text-slate-400">{{ $ban->expires_at?->format('Y-m-d H:i') ?? 'ไม่มีวันหมดอายุ' }}</td>
                                <td class="px-5 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.interactions.bans.destroy', $ban) }}" onsubmit="return confirm('ปลดบล็อกหรือไม่?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-white/10">ปลดบล็อก</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">ไม่พบรายการบล็อกตามตัวกรอง</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $bans->links() }}
    </div>
</x-layouts.admin>
