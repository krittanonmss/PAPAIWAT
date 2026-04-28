<x-layouts.admin title="จัดการผู้ใช้งาน">
    <div class="space-y-6 text-white">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">จัดการผู้ใช้งาน</h1>
                <p class="text-sm text-slate-400">จัดการผู้ดูแลระบบและสิทธิ์การเข้าถึง</p>
            </div>

            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:opacity-90">
                +
                เพิ่มผู้ใช้ใหม่
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                <p class="text-sm text-slate-400">ผู้ใช้ทั้งหมด</p>
                <p class="mt-2 text-2xl font-bold">{{ $admins->total() }}</p>
            </div>

            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-5">
                <p class="text-sm text-emerald-300">ใช้งานอยู่</p>
                <p class="mt-2 text-2xl font-bold text-emerald-300">
                    {{ $admins->where('status','active')->count() }}
                </p>
            </div>

            <div class="rounded-2xl border border-red-400/20 bg-red-500/10 p-5">
                <p class="text-sm text-red-300">ไม่ใช้งาน</p>
                <p class="mt-2 text-2xl font-bold text-red-300">
                    {{ $admins->where('status','inactive')->count() }}
                </p>
            </div>

            <div class="rounded-2xl border border-yellow-400/20 bg-yellow-500/10 p-5">
                <p class="text-sm text-yellow-300">Super Admin</p>
                <p class="mt-2 text-2xl font-bold text-yellow-300">
                    {{ $admins->filter(fn($a) => $a->role?->name === 'Super Admin')->count() }}
                </p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 backdrop-blur">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="ค้นหาผู้ใช้..."
                    class="rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2 text-sm text-white placeholder-slate-500 focus:border-blue-400 focus:outline-none"
                >

                <select
                    name="role_id"
                    class="rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                >
                    <option value="">บทบาท: ทั้งหมด</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) request('role_id') === (string) $role->id)>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                <select
                    name="status"
                    class="rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2 text-sm text-white focus:border-blue-400 focus:outline-none"
                >
                    <option value="">สถานะ: ทั้งหมด</option>
                    <option value="active" @selected(request('status')==='active')>ใช้งานอยู่</option>
                    <option value="inactive" @selected(request('status')==='inactive')>ไม่ใช้งาน</option>
                </select>

            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] backdrop-blur">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/10 text-slate-400">
                        <tr>
                            <th class="px-5 py-3 text-left">ผู้ใช้</th>
                            <th class="px-5 py-3 text-left">บทบาท</th>
                            <th class="px-5 py-3 text-left">สถานะ</th>
                            <th class="px-5 py-3 text-left">เข้าสู่ระบบล่าสุด</th>
                            <th class="px-5 py-3 text-right">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse ($admins as $admin)
                            <tr class="hover:bg-white/5 transition">

                                {{-- User --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-semibold">
                                            {{ strtoupper(substr($admin->username,0,1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-white">{{ $admin->username }}</p>
                                            <p class="text-xs text-slate-400">{{ $admin->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-medium border border-white/10 bg-white/5">
                                        {{ $admin->role?->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4">
                                    @if ($admin->status === 'active')
                                        <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs text-emerald-300 border border-emerald-400/20">
                                            ● ใช้งานอยู่
                                        </span>
                                    @else
                                        <span class="rounded-full bg-red-500/10 px-3 py-1 text-xs text-red-300 border border-red-400/20">
                                            ● ไม่ใช้งาน
                                        </span>
                                    @endif
                                </td>

                                {{-- Last login --}}
                                <td class="px-5 py-4 text-slate-400">
                                    {{ $admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : '-' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.users.show',$admin) }}"
                                           class="rounded-lg border border-white/10 px-3 py-1.5 text-xs hover:bg-white/5">
                                            ดู
                                        </a>

                                        <a href="{{ route('admin.users.edit',$admin) }}"
                                           class="rounded-lg border border-white/10 px-3 py-1.5 text-xs hover:bg-white/5">
                                            แก้ไข
                                        </a>

                                        <form method="POST" action="{{ route('admin.users.destroy',$admin) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                onclick="return confirm('ลบผู้ใช้นี้หรือไม่?')"
                                                class="rounded-lg border border-red-400/20 px-3 py-1.5 text-xs text-red-300 hover:bg-red-500/10"
                                            >
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">
                                    ไม่พบข้อมูลผู้ใช้งาน
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
</x-layouts.admin>