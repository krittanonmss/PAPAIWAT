@php
    $view = 'frontend.templates.sections.' . str_replace('-', '_', $section->component_key);
    $settings = $section->settings_data ?? [];
    $visibilityClass = match ($settings['visibility'] ?? 'all') {
        'desktop' => 'hidden md:block',
        'mobile' => 'block md:hidden',
        'hidden' => 'hidden',
        default => '',
    };
    $animationType = in_array(($settings['animation_type'] ?? 'none'), ['none', 'fade', 'fade-up', 'fade-down', 'slide-left', 'slide-right', 'zoom-in', 'zoom-out'], true)
        ? ($settings['animation_type'] ?? 'none')
        : 'none';
    $animationClass = preg_match('/^[a-zA-Z0-9_-]{1,64}$/', (string) ($settings['animation_class'] ?? ''))
        ? (string) $settings['animation_class']
        : '';
    $duration = max(100, min((int) ($settings['animation_duration'] ?? 500), 3000));
    $delay = max(0, min((int) ($settings['animation_delay'] ?? 0), 3000));
    $customAnimationCss = trim((string) ($settings['custom_animation_css'] ?? ''));
    $customAnimationClass = $customAnimationCss !== ''
        ? 'cms-section-custom-' . ($section->id ?: md5($section->section_key ?? $section->component_key ?? uniqid()))
        : '';
    $scopedCustomAnimationCss = '';

    if ($customAnimationCss !== '' && $customAnimationClass !== '') {
        $customAnimationCss = str_replace(["</style", "<script", "</script"], '', $customAnimationCss);
        $scopedCustomAnimationCss = str_replace('{section}', '.' . $customAnimationClass, $customAnimationCss);
    }
@endphp

@once
    <style>
        @media (prefers-reduced-motion: no-preference) {
            .cms-section-animate {
                animation-duration: var(--cms-animation-duration, 500ms);
                animation-delay: var(--cms-animation-delay, 0ms);
                animation-fill-mode: both;
                animation-timing-function: cubic-bezier(0.22, 1, 0.36, 1);
            }
            .cms-anim-fade { animation-name: cmsFade; }
            .cms-anim-fade-up { animation-name: cmsFadeUp; }
            .cms-anim-fade-down { animation-name: cmsFadeDown; }
            .cms-anim-slide-left { animation-name: cmsSlideLeft; }
            .cms-anim-slide-right { animation-name: cmsSlideRight; }
            .cms-anim-zoom-in { animation-name: cmsZoomIn; }
            .cms-anim-zoom-out { animation-name: cmsZoomOut; }
            .cms-section-root a[class*="rounded-"],
            .cms-section-root button[class*="rounded-"] {
                background: var(--section-button-bg, initial);
                border-color: var(--section-button-border, currentColor);
                border-radius: var(--section-button-radius, inherit);
                color: var(--section-button-color, inherit);
            }
            .cms-section-root section > .mx-auto[class*="max-w-"] {
                max-width: var(--section-layout-width, inherit);
            }
            @keyframes cmsFade { from { opacity: 0; } to { opacity: 1; } }
            @keyframes cmsFadeUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes cmsFadeDown { from { opacity: 0; transform: translateY(-24px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes cmsSlideLeft { from { opacity: 0; transform: translateX(32px); } to { opacity: 1; transform: translateX(0); } }
            @keyframes cmsSlideRight { from { opacity: 0; transform: translateX(-32px); } to { opacity: 1; transform: translateX(0); } }
            @keyframes cmsZoomIn { from { opacity: 0; transform: scale(0.96); } to { opacity: 1; transform: scale(1); } }
            @keyframes cmsZoomOut { from { opacity: 0; transform: scale(1.04); } to { opacity: 1; transform: scale(1); } }
        }
    </style>
@endonce

@if($scopedCustomAnimationCss !== '')
    <style>
        @media (prefers-reduced-motion: no-preference) {
            {!! $scopedCustomAnimationCss !!}
        }
    </style>
@endif

@if(view()->exists($view))
    <div
        class="cms-section-root {{ $visibilityClass }} {{ $animationType !== 'none' ? 'cms-section-animate cms-anim-' . $animationType : '' }} {{ $animationClass }} {{ $customAnimationClass }}"
        style="{{ $animationType !== 'none' ? '--cms-animation-duration: ' . $duration . 'ms; --cms-animation-delay: ' . $delay . 'ms;' : '' }}"
    >
        @include($view, ['section' => $section])
    </div>
@endif
