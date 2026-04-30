<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->meta_title ?? $page->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white">
    <main>
        @forelse ($sections as $section)
            @php
                $settings = $section->settings ?? [];
                $content = $section->content ?? [];
                $data = $sectionData[$section->id] ?? null;
            @endphp

            @includeIf('frontend.sections.' . $section->component_key, [
                'page' => $page,
                'section' => $section,
                'settings' => $settings,
                'content' => $content,
                'data' => $data,
            ])

            @unless (view()->exists('frontend.sections.' . $section->component_key))
                <section class="px-6 py-10">
                    <div class="mx-auto max-w-5xl rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4 text-sm text-amber-200">
                        ยังไม่มี renderer สำหรับ component_key: {{ $section->component_key }}
                    </div>
                </section>
            @endunless
        @empty
            <section class="px-6 py-20 text-center">
                <h1 class="text-3xl font-bold text-white">{{ $page->title }}</h1>
                <p class="mt-3 text-slate-400">หน้านี้ยังไม่มี section</p>
            </section>
        @endforelse
    </main>
</body>
</html>