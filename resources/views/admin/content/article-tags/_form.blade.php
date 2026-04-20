@php
    /** @var \App\Models\Content\Article\ArticleTag|null $articleTag */
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-6">
    <div class="mb-5">
        <h2 class="text-lg font-semibold text-slate-900">Tag Information</h2>
        <p class="text-sm text-slate-500">
            Fill in the basic details for this article tag.
        </p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">
                Tag Name <span class="text-rose-500">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $articleTag->name ?? '') }}"
                class="@error('name') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                placeholder="Enter tag name"
            >
            @error('name')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-700">
                Slug
            </label>
            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ old('slug', $articleTag->slug ?? '') }}"
                class="@error('slug') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-slate-400"
                placeholder="Leave blank to auto generate"
            >
            <p class="mt-1.5 text-xs text-slate-500">
                Used for unique tag URL key. Leave empty to generate automatically from the name.
            </p>
            @error('slug')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-700">
                Sort Order
            </label>
            <input
                type="number"
                id="sort_order"
                name="sort_order"
                value="{{ old('sort_order', $articleTag->sort_order ?? 0) }}"
                min="0"
                class="@error('sort_order') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
            >
            @error('sort_order')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">
                Status <span class="text-rose-500">*</span>
            </label>
            <select
                id="status"
                name="status"
                class="@error('status') border-rose-300 @else border-slate-300 @enderror w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400"
            >
                <option value="active" @selected(old('status', $articleTag->status ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $articleTag->status ?? 'active') === 'inactive')>Inactive</option>
            </select>
            @error('status')
                <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>