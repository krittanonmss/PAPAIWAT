<x-layouts.admin :title="'Edit Menu Item'">
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Edit Menu Item</h1>
            <p class="text-sm text-slate-500">
                {{ $menuItem->label }} (Menu: {{ $menu->name }})
            </p>
        </div>

        <form method="POST"
              action="{{ route('admin.content.menu-items.update', [$menu, $menuItem]) }}"
              class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6">
            @csrf
            @method('PUT')

            {{-- Parent --}}
            <div>
                <label class="text-sm font-medium">Parent</label>
                <select name="parent_id" class="mt-1 w-full rounded-xl border">
                    <option value="">-- Root --</option>
                    @foreach($parentItems as $item)
                        <option value="{{ $item->id }}"
                            {{ $menuItem->parent_id == $item->id ? 'selected' : '' }}>
                            {{ $item->label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Label --}}
            <div>
                <label class="text-sm font-medium">Label</label>
                <input name="label" value="{{ $menuItem->label }}" class="mt-1 w-full rounded-xl border">
            </div>

            {{-- Type --}}
            <div>
                <label class="text-sm font-medium">Type</label>
                <select name="menu_item_type" class="mt-1 w-full rounded-xl border">
                    @foreach(['route','page','content','external_url','anchor'] as $type)
                        <option value="{{ $type }}"
                            {{ $menuItem->menu_item_type === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Target --}}
            <div>
                <label class="text-sm font-medium">Target</label>
                <select name="target" class="mt-1 w-full rounded-xl border">
                    <option value="_self" {{ $menuItem->target === '_self' ? 'selected' : '' }}>Same Tab</option>
                    <option value="_blank" {{ $menuItem->target === '_blank' ? 'selected' : '' }}>New Tab</option>
                </select>
            </div>

            {{-- Enabled --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_enabled" value="1"
                       {{ $menuItem->is_enabled ? 'checked' : '' }}>
                <label>Enabled</label>
            </div>

            <div class="flex justify-between">
                <form method="POST"
                      action="{{ route('admin.content.menu-items.destroy', [$menu, $menuItem]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Delete</button>
                </form>

                <div class="flex gap-2">
                    <a href="{{ route('admin.content.menus.show', $menu) }}"
                       class="px-4 py-2 border rounded-xl">Cancel</a>

                    <button class="px-4 py-2 bg-slate-900 text-white rounded-xl">
                        Update
                    </button>
                </div>
            </div>

        </form>
    </div>
</x-layouts.admin>