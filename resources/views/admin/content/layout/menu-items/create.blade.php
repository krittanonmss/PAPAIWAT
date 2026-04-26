<x-layouts.admin :title="'Create Menu Item'">
    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Create Menu Item</h1>
            <p class="text-sm text-slate-500">Menu: {{ $menu->name }}</p>
        </div>

        <form method="POST"
              action="{{ route('admin.content.menu-items.store', $menu) }}"
              class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6">
            @csrf

            {{-- Parent --}}
            <div>
                <label class="text-sm font-medium">Parent</label>
                <select name="parent_id" class="mt-1 w-full rounded-xl border">
                    <option value="">-- Root --</option>
                    @foreach($parentItems as $item)
                        <option value="{{ $item->id }}">{{ $item->label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Label --}}
            <div>
                <label class="text-sm font-medium">Label</label>
                <input name="label" class="mt-1 w-full rounded-xl border">
            </div>

            {{-- Type --}}
            <div>
                <label class="text-sm font-medium">Type</label>
                <select name="menu_item_type" class="mt-1 w-full rounded-xl border">
                    <option value="route">Route</option>
                    <option value="page">Page</option>
                    <option value="content">Content</option>
                    <option value="external_url">External URL</option>
                    <option value="anchor">Anchor</option>
                </select>
            </div>

            {{-- Route --}}
            <div>
                <label class="text-sm font-medium">Route Name</label>
                <input name="route_name" class="mt-1 w-full rounded-xl border">
            </div>

            {{-- Page --}}
            <div>
                <label class="text-sm font-medium">Page</label>
                <select name="page_id" class="mt-1 w-full rounded-xl border">
                    <option value="">-- None --</option>
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}">{{ $page->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Content --}}
            <div>
                <label class="text-sm font-medium">Content</label>
                <select name="content_id" class="mt-1 w-full rounded-xl border">
                    <option value="">-- None --</option>
                    @foreach($contents as $content)
                        <option value="{{ $content->id }}">{{ $content->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- URL --}}
            <div>
                <label class="text-sm font-medium">External URL</label>
                <input name="external_url" class="mt-1 w-full rounded-xl border">
            </div>

            {{-- Target --}}
            <div>
                <label class="text-sm font-medium">Target</label>
                <select name="target" class="mt-1 w-full rounded-xl border">
                    <option value="_self">Same Tab</option>
                    <option value="_blank">New Tab</option>
                </select>
            </div>

            {{-- Enabled --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_enabled" value="1" checked>
                <label>Enabled</label>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.content.menus.show', $menu) }}"
                   class="px-4 py-2 border rounded-xl">Cancel</a>

                <button class="px-4 py-2 bg-slate-900 text-white rounded-xl">
                    Save
                </button>
            </div>

        </form>
    </div>
</x-layouts.admin>