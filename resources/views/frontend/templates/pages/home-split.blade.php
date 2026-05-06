@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'PAPAIWAT')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'PAPAIWAT Platform')

@section('content')
    @php
        $sections = collect($sections ?? []);
        $homeTemples = collect($homeTemples ?? []);
        $homeArticles = collect($homeArticles ?? []);
    @endphp

    @if($sections->isNotEmpty())
        <main class="bg-slate-950 text-white">
            @foreach($sections as $section)
                @include('frontend.templates.sections._renderer', ['section' => $section])
            @endforeach
        </main>
    @else
    <main class="bg-slate-950 text-white">
        <section class="grid min-h-screen lg:grid-cols-2">
            <a href="{{ url('/temple-list') }}" class="group relative flex min-h-[50vh] items-end overflow-hidden border-b border-white/10 bg-slate-900 p-6 lg:min-h-screen lg:border-b-0 lg:border-r">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-950/45 to-slate-950"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/55 to-transparent"></div>
                <div class="relative max-w-xl">
                    <p class="text-sm font-medium text-blue-300">Temples</p>
                    <h1 class="mt-3 text-4xl font-bold leading-tight md:text-6xl">วัดแนะนำ</h1>
                    <p class="mt-4 text-base leading-7 text-slate-300">สำรวจวัดเด่น วัดยอดนิยม และข้อมูลสถานที่จากฐานข้อมูล</p>
                    <span class="mt-7 inline-flex rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold transition group-hover:bg-blue-500">สำรวจวัด</span>
                </div>
            </a>

            <a href="{{ url('/article-list') }}" class="group relative flex min-h-[50vh] items-end overflow-hidden bg-slate-900 p-6 lg:min-h-screen">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-950/50 to-slate-950"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/55 to-transparent"></div>
                <div class="relative max-w-xl">
                    <p class="text-sm font-medium text-blue-300">Articles</p>
                    <h1 class="mt-3 text-4xl font-bold leading-tight md:text-6xl">Article แนะนำ</h1>
                    <p class="mt-4 text-base leading-7 text-slate-300">อ่านเรื่องราว บทความ และข้อมูลวัฒนธรรมที่คัดจากระบบ</p>
                    <span class="mt-7 inline-flex rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-3 text-sm font-semibold transition group-hover:bg-white/10">อ่านบทความ</span>
                </div>
            </a>
        </section>

        <section class="mx-auto grid max-w-7xl gap-8 px-4 py-14 lg:grid-cols-2">
            <div>
                <div class="mb-5 flex items-end justify-between gap-3">
                    <h2 class="text-3xl font-bold">วัดล่าสุด</h2>
                    <a href="{{ url('/temple-list') }}" class="text-sm text-slate-300 hover:text-white">ดูทั้งหมด</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @forelse ($homeTemples->take(4) as $temple)
                        @php
                            $content = $temple->relationLoaded('content') ? $temple->content : null;
                            $address = $temple->relationLoaded('address') ? $temple->address : null;
                        @endphp
                        <a href="{{ route('temples.show', $temple) }}" class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 transition hover:border-blue-300/40 hover:bg-white/[0.07]">
                            <h3 class="line-clamp-2 text-xl font-medium">{{ $content?->title ?? '-' }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $content?->excerpt ?? 'ยังไม่มีคำโปรย' }}</p>
                            <p class="mt-3 text-xs text-blue-200">{{ $address?->province ?? 'ไม่ระบุจังหวัด' }}</p>
                        </a>
                    @empty
                        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 sm:col-span-2">ยังไม่มีวัดแนะนำ</div>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="mb-5 flex items-end justify-between gap-3">
                    <h2 class="text-3xl font-bold">บทความล่าสุด</h2>
                    <a href="{{ url('/article-list') }}" class="text-sm text-slate-300 hover:text-white">ดูทั้งหมด</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @forelse ($homeArticles->take(4) as $articleContent)
                        <a href="{{ route('articles.show', $articleContent->slug) }}" class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 transition hover:border-blue-300/40 hover:bg-white/[0.07]">
                            <p class="text-xs text-blue-300">{{ $articleContent->published_at?->format('d M Y') ?? 'Published' }}</p>
                            <h3 class="mt-2 line-clamp-2 text-xl font-medium">{{ $articleContent->title }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-400">{{ $articleContent->excerpt ?? 'ยังไม่มีคำโปรย' }}</p>
                        </a>
                    @empty
                        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center text-slate-400 sm:col-span-2">ยังไม่มี Article แนะนำ</div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
    @endif
@endsection
