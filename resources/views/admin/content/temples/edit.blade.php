<x-layouts.admin :title="$title" header="Edit Temple">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Edit Temple</h1>
                <p class="text-sm text-slate-500">
                    แก้ไขข้อมูลวัด: <span class="font-medium text-slate-700">{{ $temple->content?->title }}</span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.temples.show', $temple) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    View
                </a>
                <a
                    href="{{ route('admin.temples.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Back to List
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-medium">กรุณาตรวจสอบข้อมูลที่กรอก</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.temples.update', $temple) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @include('admin.content.temples._form')

            <div class="flex items-center justify-between">
                {{-- ปุ่ม Delete — ใช้ JS submit form แยกต่างหาก ไม่ซ้อน <form> ใน <form> --}}
                <button
                    type="button"
                    onclick="if(confirm('ยืนยันการลบข้อมูลวัดนี้? ไม่สามารถกู้คืนได้')) document.getElementById('delete-temple-form').submit()"
                    class="inline-flex items-center justify-center rounded-xl border border-rose-300 px-5 py-2.5 text-sm font-medium text-rose-700 hover:bg-rose-50"
                >
                    Delete Temple
                </button>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('admin.temples.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800"
                    >
                        Update Temple
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Delete form อยู่นอก update form เพื่อหลีกเลี่ยง nested <form> --}}
    <form
        id="delete-temple-form"
        method="POST"
        action="{{ route('admin.temples.destroy', $temple) }}"
        class="hidden"
    >
        @csrf
        @method('DELETE')
    </form>
</x-layouts.admin>