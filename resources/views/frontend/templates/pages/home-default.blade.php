@extends('frontend.layouts.app')

@section('title', $page->meta_title ?? $page->title ?? 'PAPAIWAT')
@section('meta_description', $page->meta_description ?? $page->excerpt ?? 'PAPAIWAT Platform')

@section('content')
    <div class="space-y-10">
        @foreach ($sections as $section)
            @includeIf('frontend.sections.' . str_replace('.', '/', $section->component_key), [
                'section' => $section,
                'content' => $section->content ?? [],
                'settings' => $section->settings ?? [],
                'data' => $sectionData[$section->id] ?? null,
            ])
        @endforeach
    </div>
@endsection