<x-layouts.admin title="แดชบอร์ดผู้ดูแล" header="Dashboard">
    @php
        $statusStyles = [
            'published' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300',
            'draft' => 'border-yellow-400/20 bg-yellow-500/10 text-yellow-300',
            'archived' => 'border-white/10 bg-white/5 text-slate-300',
            'success' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300',
            'failed' => 'border-red-400/20 bg-red-500/10 text-red-300',
        ];

        $metricCards = [
            ['label' => 'วัดทั้งหมด', 'value' => $stats['temples'], 'hint' => 'ข้อมูลวัดในระบบ'],
            ['label' => 'บทความ', 'value' => $stats['articles'], 'hint' => 'บทความทั้งหมด'],
            ['label' => 'หน้าเว็บไซต์', 'value' => $stats['pages'], 'hint' => 'pages ที่จัดการได้'],
            ['label' => 'ไฟล์สื่อ', 'value' => $stats['media'], 'hint' => 'คลังสื่อ ทั้งหมด'],
        ];
    @endphp

    <div class="space-y-6 text-white">
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
            <div class="border-b border-white/10 bg-gradient-to-r from-slate-900 via-slate-900 to-indigo-950 px-6 py-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-300">แดชบอร์ดผู้ดูแล</p>
                        <h1 class="mt-1 text-2xl font-bold text-white">ภาพรวมระบบผู้ดูแล</h1>
                        <p class="mt-2 max-w-3xl text-sm text-slate-400">
                            สรุปสถานะเนื้อหา คลังสื่อ ความเคลื่อนไหว และความปลอดภัยล่าสุดที่ผู้ดูแลควรเห็นก่อนเริ่มงาน
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a
                            href="{{ route('admin.temples.create') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white"
                        >
                            + เพิ่มวัด
                        </a>

                        <a
                            href="{{ route('admin.media.create') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:opacity-90"
                        >
                            + อัปโหลดสื่อ
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 p-4 md:grid-cols-4">
                @foreach ($metricCards as $metric)
                    <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $metric['label'] }}</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ number_format($metric['value']) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $metric['hint'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">สถานะเนื้อหา</p>
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4">
                        <p class="text-xs text-emerald-300">เผยแพร่</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-300">{{ number_format($stats['published_content']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-yellow-400/20 bg-yellow-500/10 p-4">
                        <p class="text-xs text-yellow-300">ฉบับร่าง</p>
                        <p class="mt-1 text-2xl font-bold text-yellow-300">{{ number_format($stats['draft_content']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                        <p class="text-xs text-slate-400">เก็บถาวร</p>
                        <p class="mt-1 text-2xl font-bold text-white">{{ number_format($stats['archived_content']) }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">ความปลอดภัย 24 ชั่วโมง</p>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4">
                        <p class="text-xs text-emerald-300">เข้าสู่ระบบสำเร็จ</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-300">{{ number_format($stats['successful_logins_24h']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-red-400/20 bg-red-500/10 p-4">
                        <p class="text-xs text-red-300">เข้าสู่ระบบไม่สำเร็จ</p>
                        <p class="mt-1 text-2xl font-bold text-red-300">{{ number_format($stats['failed_logins_24h']) }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-lg shadow-slate-950/20 backdrop-blur">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">ผู้ดูแลระบบ</p>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                        <p class="text-xs text-slate-400">ผู้ดูแลทั้งหมด</p>
                        <p class="mt-1 text-2xl font-bold text-white">{{ number_format($stats['admins']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 p-4">
                        <p class="text-xs text-blue-300">เซสชันที่ใช้งาน</p>
                        <p class="mt-1 text-2xl font-bold text-blue-300">{{ number_format($stats['active_sessions']) }}</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
            <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                <div class="flex flex-col gap-1 border-b border-white/10 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-white">เนื้อหาที่แก้ไขล่าสุด</h2>
                        <p class="text-sm text-slate-400">ใช้ตรวจงานที่เพิ่งมีการเปลี่ยนแปลง</p>
                    </div>
                    <a href="{{ route('admin.temples.index') }}" class="text-sm font-medium text-blue-300 hover:text-blue-200">ไปจัดการเนื้อหา</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-950/30 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">เนื้อหา</th>
                                <th class="px-4 py-3 text-left">ประเภท</th>
                                <th class="px-4 py-3 text-left">สถานะ</th>
                                <th class="px-4 py-3 text-left">แก้ไขล่าสุด</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 text-slate-300">
                            @forelse ($recentContents as $content)
                                <tr class="transition hover:bg-white/[0.06]">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-white">{{ $content->title }}</p>
                                        <p class="mt-1 text-xs text-slate-500">/{{ $content->slug }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-slate-300">
                                            {{ ucfirst($content->content_type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-medium {{ $statusStyles[$content->status] ?? 'border-white/10 bg-white/5 text-slate-300' }}">
                                            {{ ucfirst($content->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-400">
                                        {{ optional($content->updated_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">ยังไม่มีเนื้อหา</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="space-y-6">
                <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                    <div class="border-b border-white/10 px-5 py-4">
                        <h2 class="text-base font-semibold text-white">สื่อล่าสุด</h2>
                        <p class="text-sm text-slate-400">ไฟล์ที่อัปโหลดล่าสุดใน คลังสื่อ</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 p-4">
                        @forelse ($recentMedia as $media)
                            @php
                                $previewUrl = $media->media_type === 'image' && $media->path
                                    ? asset('storage/' . $media->path)
                                    : null;
                            @endphp
                            <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/30">
                                <div class="aspect-[4/3] bg-slate-950">
                                    @if ($previewUrl)
                                        <img src="{{ $previewUrl }}" alt="{{ $media->alt_text ?: $media->title ?: $media->original_filename }}" class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <div class="flex h-full items-center justify-center text-xs font-semibold text-slate-500">
                                            {{ strtoupper($media->extension ?: 'FILE') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="truncate text-xs font-medium text-white">{{ $media->title ?: $media->original_filename }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ optional($media->uploaded_at)->format('d/m/Y H:i') ?? '-' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-2 p-5 text-center text-sm text-slate-500">ยังไม่มีไฟล์สื่อ</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                <div class="border-b border-white/10 px-5 py-4">
                    <h2 class="text-base font-semibold text-white">ประวัติการเข้าสู่ระบบ</h2>
                    <p class="text-sm text-slate-400">รายการ login ล่าสุด</p>
                </div>
                <div class="divide-y divide-white/10">
                    @forelse ($loginLogs as $log)
                        <div class="flex items-center justify-between gap-4 px-5 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-white">{{ $log->admin?->username ?? $log->email }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $log->ip_address ?: '-' }} · {{ optional($log->created_at)->format('d/m/Y H:i') ?? '-' }}</p>
                            </div>
                            <span class="shrink-0 rounded-full border px-3 py-1 text-xs font-medium {{ $statusStyles[$log->status] ?? 'border-white/10 bg-white/5 text-slate-300' }}">
                                {{ $log->status === 'success' ? 'สำเร็จ' : 'ไม่สำเร็จ' }}
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-slate-500">ยังไม่มีประวัติการเข้าสู่ระบบ</div>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20 backdrop-blur">
                <div class="border-b border-white/10 px-5 py-4">
                    <h2 class="text-base font-semibold text-white">Activity ล่าสุด</h2>
                    <p class="text-sm text-slate-400">คำขอที่ admin เรียกใช้งานล่าสุด</p>
                </div>
                <div class="divide-y divide-white/10">
                    @forelse ($recentActivities as $activity)
                        <div class="px-5 py-3">
                            <div class="flex items-center justify-between gap-4">
                                <p class="truncate text-sm font-medium text-white">{{ $activity->admin?->username ?? '-' }}</p>
                                <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-xs text-slate-300">{{ $activity->method }}</span>
                            </div>
                            <p class="mt-1 truncate text-xs text-slate-500">{{ $activity->target }}</p>
                            <p class="mt-1 text-xs text-slate-600">{{ optional($activity->created_at)->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-slate-500">ยังไม่มี activity log</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts.admin>
