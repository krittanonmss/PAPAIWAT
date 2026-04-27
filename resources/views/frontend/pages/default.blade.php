<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->meta_title ?? $page->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-slate-900">
    <main>
        @forelse ($page->sections as $section)
            @php
                $settings = $section->settings ?? [];
                $content = $section->content ?? [];
            @endphp

            @switch($section->component_key)
                @case('hero')
                    <section class="bg-slate-950 px-6 py-24 text-white">
                        <div class="mx-auto max-w-5xl text-center">
                            <p class="mb-3 text-sm text-slate-300">
                                {{ $content['subtitle'] ?? '' }}
                            </p>

                            <h1 class="text-4xl font-bold md:text-6xl">
                                {{ $content['title'] ?? $section->name }}
                            </h1>

                            <p class="mx-auto mt-6 max-w-2xl text-slate-300">
                                {{ $content['description'] ?? '' }}
                            </p>

                            @if (! empty($content['button_text']) && ! empty($content['button_url']))
                                <a
                                    href="{{ $content['button_url'] }}"
                                    class="mt-8 inline-flex rounded-xl bg-white px-6 py-3 font-medium text-slate-950 hover:bg-slate-100"
                                >
                                    {{ $content['button_text'] }}
                                </a>
                            @endif
                        </div>
                    </section>
                @break

                @case('temple_list')
                    <section class="px-6 py-16">
                        <div class="mx-auto max-w-6xl">
                            <div class="mb-8 flex items-end justify-between gap-4">
                                <div>
                                    <h2 class="text-3xl font-bold text-slate-900">
                                        {{ $content['title'] ?? 'Temple List' }}
                                    </h2>

                                    <p class="mt-2 text-slate-500">
                                        {{ $content['subtitle'] ?? '' }}
                                    </p>
                                </div>

                                @if (! empty($content['show_view_all']) && ! empty($content['view_all_url']))
                                    <a
                                        href="{{ $content['view_all_url'] }}"
                                        class="text-sm font-medium text-slate-700 hover:text-slate-950"
                                    >
                                        ดูทั้งหมด
                                    </a>
                                @endif
                            </div>

                            <div class="grid gap-6 md:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                    <h3 class="font-semibold text-slate-900">ตัวอย่างวัดที่ 1</h3>
                                    <p class="mt-2 text-sm text-slate-500">ข้อมูลตัวอย่างสำหรับทดสอบ section</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                    <h3 class="font-semibold text-slate-900">ตัวอย่างวัดที่ 2</h3>
                                    <p class="mt-2 text-sm text-slate-500">ข้อมูลตัวอย่างสำหรับทดสอบ section</p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                    <h3 class="font-semibold text-slate-900">ตัวอย่างวัดที่ 3</h3>
                                    <p class="mt-2 text-sm text-slate-500">ข้อมูลตัวอย่างสำหรับทดสอบ section</p>
                                </div>
                            </div>
                        </div>
                    </section>
                @break

                @default
                    <section class="px-6 py-10">
                        <div class="mx-auto max-w-5xl rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            ยังไม่มี renderer สำหรับ component_key: {{ $section->component_key }}
                        </div>
                    </section>
            @endswitch
        @empty
            <section class="px-6 py-20 text-center">
                <h1 class="text-3xl font-bold text-slate-900">{{ $page->title }}</h1>
                <p class="mt-3 text-slate-500">หน้านี้ยังไม่มี section</p>
            </section>
        @endforelse
    </main>
</body>
</html>