<x-layouts.admin :title="'แก้ไขรายการเมนู'">
    <div class="space-y-6 text-white">
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">Menu Builder</p>
                    <h1 class="mt-1 text-2xl font-bold text-white">แก้ไข {{ $menuItem->label }}</h1>
                    <p class="mt-2 text-sm text-slate-400">อยู่ในเมนู {{ $menu->name }}</p>
                </div>
                <a href="{{ route('admin.content.menus.show', $menu) }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">กลับไปเมนู</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-4 text-sm text-rose-200">
                <p class="font-semibold text-rose-100">กรุณาตรวจสอบข้อมูลที่กรอก</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.content.menu-items.update', [$menu, $menuItem]) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.content.layout.menu-items._form', ['menuItem' => $menuItem])

            <div class="sticky bottom-0 z-20 -mx-2 rounded-t-3xl border border-white/10 bg-slate-950/90 px-4 py-4 shadow-2xl shadow-slate-950 backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <button type="button" onclick="if(confirm('ยืนยันการลบรายการนี้?')) document.getElementById('delete-menu-item-form').submit()" class="inline-flex items-center justify-center rounded-xl border border-rose-400/30 bg-rose-500/10 px-5 py-2.5 text-sm font-medium text-rose-300 transition hover:bg-rose-500/20">ลบ</button>
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.content.menus.show', $menu) }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-5 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">ยกเลิก</a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/40 transition hover:bg-blue-500">บันทึก</button>
                    </div>
                </div>
            </div>
        </form>

        <form id="delete-menu-item-form" method="POST" action="{{ route('admin.content.menu-items.destroy', [$menu, $menuItem]) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</x-layouts.admin>
