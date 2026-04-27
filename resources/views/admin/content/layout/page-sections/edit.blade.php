<x-layouts.admin :title="'Edit Page Section'">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Edit Page Section</h1>
                <p class="text-sm text-slate-500">
                    แก้ไข section: {{ $section->name }}
                </p>
            </div>

            <a
                href="{{ route('admin.content.pages.show', $page) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                Back
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form
                id="update-section-form"
                method="POST"
                action="{{ route('admin.content.pages.sections.update', [$page, $section]) }}"
            >
                @csrf
                @method('PUT')

                @include('admin.content.layout.page-sections._form', ['section' => $section])
            </form>

            <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                <form
                    method="POST"
                    action="{{ route('admin.content.pages.sections.destroy', [$page, $section]) }}"
                    onsubmit="return confirm('ยืนยันการลบ section นี้?')"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-100"
                    >
                        Delete Section
                    </button>
                </form>

                <div class="flex items-center justify-end gap-3">
                    <a
                        href="{{ route('admin.content.pages.show', $page) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        form="update-section-form"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        Update Section
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>