<x-layouts.admin :title="'Edit Page'">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Edit Page</h1>
                <p class="text-sm text-slate-500">
                    แก้ไขหน้าเว็บไซต์: {{ $page->title }}
                </p>
            </div>

            <a
                href="{{ route('admin.content.pages.show', $page) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                Back
            </a>
        </div>

        <form
            method="POST"
            action="{{ route('admin.content.pages.update', $page) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            @include('admin.content.layout.pages._form', ['page' => $page])

            <div class="flex items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <a
                    href="{{ route('admin.content.pages.show', $page) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Update Page
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>