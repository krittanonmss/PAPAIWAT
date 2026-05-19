@php
    $frontendMenuItems = collect($frontendMenuItems ?? []);
@endphp

<nav aria-label="Main navigation">
    @if ($frontendMenuItems->isNotEmpty())
        <ul class="flex items-center gap-5 text-sm text-slate-300">
            @include('frontend.partials._navigation_items', [
                'items' => $frontendMenuItems,
                'level' => 0,
            ])
        </ul>
    @else
        <ul class="flex items-center gap-5 text-sm text-slate-300">
            <li><a href="{{ route('home') }}" class="hover:text-white">หน้าแรก</a></li>
        </ul>
    @endif
</nav>
