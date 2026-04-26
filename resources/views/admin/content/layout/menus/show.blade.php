<x-layouts.admin :title="$menu->name">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $menu->name }}</h1>
                <p class="text-sm text-slate-500">
                    รายละเอียดกลุ่มเมนูและรายการเมนูภายใน
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('admin.content.menus.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Back
                </a>

                <a
                    href="{{ route('admin.content.menus.edit', $menu) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Edit
                </a>

                <a
                    href="{{ route('admin.content.menu-items.create', $menu) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Add Menu Item
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-1">
                <h2 class="text-base font-semibold text-slate-900">Menu Detail</h2>

                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500">Name</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $menu->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Slug</dt>
                        <dd class="mt-1">
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $menu->slug }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Location Key</dt>
                        <dd class="mt-1 text-slate-900">{{ $menu->location_key ?? '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Sort Order</dt>
                        <dd class="mt-1 text-slate-900">{{ $menu->sort_order }}</dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Default</dt>
                        <dd class="mt-1">
                            @if($menu->is_default)
                                <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                    Default
                                </span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Status</dt>
                        <dd class="mt-1">
                            @if($menu->status === 'active')
                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                    Inactive
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-slate-500">Description</dt>
                        <dd class="mt-1 text-slate-900">
                            {{ $menu->description ?: '-' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Menu Items</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            รายการเมนูที่อยู่ภายใต้กลุ่มนี้
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Label</th>
                                <th class="px-5 py-3 font-semibold">Type</th>
                                <th class="px-5 py-3 font-semibold">Target</th>
                                <th class="px-5 py-3 font-semibold">Order</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 text-right font-semibold">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($menu->items as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-slate-900">
                                            {{ $item->label }}
                                        </div>

                                        @if($item->description)
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $item->description }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                            {{ $item->menu_item_type }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-slate-600">
                                        {{ $item->target }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700">
                                        {{ $item->sort_order }}
                                    </td>

                                    <td class="px-5 py-4">
                                        @if($item->is_enabled)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                                Enabled
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                                Disabled
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-right">
                                        <a
                                            href="{{ route('admin.content.menu-items.edit', [$menu, $item]) }}"
                                            class="text-sm font-medium text-slate-700 hover:text-slate-950"
                                        >
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center">
                                        <div class="text-sm font-medium text-slate-700">
                                            ยังไม่มีรายการเมนู
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500">
                                            เพิ่มรายการเมนูแรกสำหรับกลุ่มนี้
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-red-900">Danger Zone</h2>
                    <p class="text-sm text-red-700">
                        ลบกลุ่มเมนูนี้ออกจากระบบ
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.content.menus.destroy', $menu) }}"
                    onsubmit="return confirm('ยืนยันการลบเมนูนี้?')"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700"
                    >
                        Delete Menu
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>