<x-layouts.admin :title="'Edit Template'">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Edit Template</h1>
                <p class="text-sm text-slate-500">
                    แก้ไข Template: {{ $template->name }}
                </p>
            </div>

            <a
                href="{{ route('admin.content.templates.show', $template) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                Back
            </a>
        </div>

        <form
            method="POST"
            action="{{ route('admin.content.templates.update', $template) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            @include('admin.content.layout.templates._form', ['template' => $template])

            <div class="flex items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <a
                    href="{{ route('admin.content.templates.show', $template) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Update Template
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>