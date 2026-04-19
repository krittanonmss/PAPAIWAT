<x-layouts.admin :title="$title" header="Temple Management">
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <form method="GET" action="{{ route('admin.temples.index') }}" class="grid flex-1 grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="search" class="mb-1 block text-sm font-medium text-slate-700">Search</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-slate-900"
                        placeholder="Temple title, slug, excerpt"
                    >
                </div>

                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-slate-900"
                    >
                        <option value="">All Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category_id" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                    <select
                        id="category_id"
                        name="category_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-slate-900"
                    >
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sort" class="mb-1 block text-sm font-medium text-slate-700">Sort</label>
                    <select
                        id="sort"
                        name="sort"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-slate-900"
                    >
                        <option value="">Latest</option>
                        <option value="popular" @selected(request('sort') === 'popular')>Popular</option>
                        <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                    </select>
                </div>

                <div class="md:col-span-2 xl:col-span-4 flex flex-wrap gap-2">
                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admin.temples.index') }}"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Reset
                    </a>
                </div>
            </form>

            <div class="shrink-0">
                <a
                    href="{{ route('admin.temples.create') }}"
                    class="inline-flex rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    + Create Temple
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Temple</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Category</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Featured</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700">Published</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($temples as $temple)
                            @php
                                $content = $temple->content;
                                $primaryCategory = $content?->categories?->firstWhere('pivot.is_primary', true);
                            @endphp
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-slate-900">
                                        {{ $content?->title ?? '-' }}
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        slug: {{ $content?->slug ?? '-' }}
                                    </div>
                                    @if ($temple->address?->province)
                                        <div class="mt-1 text-xs text-slate-500">
                                            {{ $temple->address->province }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ $primaryCategory?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                        {{ $content?->status ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ $content?->is_featured ? 'Yes' : 'No' }}
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ $content?->published_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('admin.temples.show', $temple) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50"
                                        >
                                            View
                                        </a>
                                        <a
                                            href="{{ route('admin.temples.edit', $temple) }}"
                                            class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-medium text-white hover:bg-slate-800"
                                        >
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.temples.destroy', $temple) }}" onsubmit="return confirm('ยืนยันการลบข้อมูลวัดนี้?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="rounded-lg border border-rose-300 px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-50"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                    ยังไม่มีข้อมูลวัด
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($temples->hasPages())
                <div class="border-t border-slate-200 px-4 py-4">
                    {{ $temples->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>