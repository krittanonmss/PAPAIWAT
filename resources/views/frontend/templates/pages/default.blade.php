@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'PAPAIWAT')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'PAPAIWAT Platform')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16">
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 shadow-xl shadow-slate-950/30 backdrop-blur">
            <h1 class="text-3xl font-bold text-white">
                {{ $page->title }}
            </h1>

            @if ($page->excerpt)
                <p class="mt-4 text-slate-400">
                    {{ $page->excerpt }}
                </p>
            @endif

            @if ($page->description)
                <div class="mt-8 text-sm leading-7 text-slate-300">
                    {!! nl2br(e($page->description)) !!}
                </div>
            @endif
        </div>

    </section>
@endsection
