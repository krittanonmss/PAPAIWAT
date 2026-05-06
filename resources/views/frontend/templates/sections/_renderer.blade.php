@php
    $view = 'frontend.templates.sections.' . str_replace('-', '_', $section->component_key);
@endphp

@if(view()->exists($view))
    @include($view, ['section' => $section])
@endif
