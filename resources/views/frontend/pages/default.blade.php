@extends('frontend.layouts.app')

@section('content')
    <main>
        @foreach($page->sections as $section)
            @includeIf('frontend.sections.' . $section->component_key . '.' . $section->component_key, [
                'section' => $section,
                'page' => $page,
            ])
        @endforeach
    </main>
@endsection