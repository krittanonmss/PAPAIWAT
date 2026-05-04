@extends('frontend.layouts.app')

@section('title', 'Template Preview')
@section('meta_description', 'Admin template preview')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-10">
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 text-center shadow-xl shadow-slate-950/30 backdrop-blur">
            <p class="text-sm text-blue-300">Preview-safe Template</p>
            <h1 class="mt-3 text-3xl font-bold text-white">{{ $previewTitle ?? 'Admin iframe preview' }}</h1>
            <p class="mt-4 text-sm leading-6 text-slate-400">
                {{ $previewMessage ?? 'Template นี้ใช้เป็น safe empty state สำหรับ iframe preview เท่านั้น' }}
            </p>
        </div>
    </section>
@endsection
