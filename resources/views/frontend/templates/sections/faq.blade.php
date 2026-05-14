@php
    $content = $section->content_data ?? [];
    $items = collect(preg_split("/\r\n|\n|\r/", trim($content['faq_text'] ?? '')))
        ->map(function ($line) {
            [$question, $answer] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
            return ['question' => $question, 'answer' => $answer];
        })
        ->filter(fn ($item) => $item['question'] !== '');
    $emptyText = trim((string) ($content['empty_text'] ?? '')) ?: 'ยังไม่มีคำถาม';
@endphp
<section class="px-4 py-16" style="@include('frontend.templates.sections._background')">
    <div class="mx-auto max-w-7xl">
        <div class="max-w-4xl">
        @if(!empty($content['eyebrow']))
            <p class="text-sm font-semibold text-blue-300">{{ $content['eyebrow'] }}</p>
        @endif
        <h2 class="mt-2 text-3xl font-bold text-white">{{ $content['title'] ?? 'คำถามที่พบบ่อย' }}</h2>
        @if(!empty($content['subtitle']))
            <p class="mt-3 text-sm leading-6 text-slate-400">{{ $content['subtitle'] }}</p>
        @endif

        <div class="mt-8 space-y-3">
            @forelse($items as $item)
                <details class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                    <summary class="cursor-pointer text-base font-semibold text-white">{{ $item['question'] }}</summary>
                    @if($item['answer'])
                        <p class="mt-3 text-sm leading-7 text-slate-400">{{ $item['answer'] }}</p>
                    @endif
                </details>
            @empty
                <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400">{{ $emptyText }}</div>
            @endforelse
        </div>
        </div>
    </div>
</section>
