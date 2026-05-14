@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'รายการโปรด')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'รายการโปรดที่บันทึกไว้ในเบราว์เซอร์ของคุณ')

@section('content')
    @php
        $sections = collect($sections ?? []);
    @endphp

    <main class="bg-slate-950 text-white">
        @foreach($sections as $section)
            @include('frontend.templates.sections._renderer', ['section' => $section])
        @endforeach
    </main>
@endsection
