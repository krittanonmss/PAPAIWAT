@php
    $sectionSettings = $settings ?? ($section->settings_data ?? []);
    $normalizeColor = function ($value, string $fallback) {
        $value = trim((string) $value);

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $fallback;
    };

    $legacyFallback = match ($sectionSettings['background'] ?? 'dark') {
        'plain' => '#ffffff',
        'soft' => '#f1f5f9',
        default => '#020617',
    };
    $startColor = $normalizeColor($sectionSettings['background_color'] ?? null, $legacyFallback);
    $endColor = $normalizeColor($sectionSettings['background_color_end'] ?? null, $startColor);
    $useGradient = (bool) ($sectionSettings['background_gradient'] ?? false);
    $rawDirection = $sectionSettings['background_gradient_direction'] ?? 'to bottom';
    $direction = in_array($rawDirection, [
        'to bottom',
        'to top',
        'to right',
        'to left',
        '135deg',
    ], true) ? $rawDirection : 'to bottom';
    $textColor = $normalizeColor($sectionSettings['text_color'] ?? null, '#ffffff');
    $headingColor = $normalizeColor($sectionSettings['heading_color'] ?? null, $textColor);
    $mutedTextColor = $normalizeColor($sectionSettings['muted_text_color'] ?? null, $textColor);
    $accentColor = $normalizeColor($sectionSettings['accent_color'] ?? null, '#93c5fd');
    $buttonBackgroundColor = $normalizeColor($sectionSettings['button_background_color'] ?? null, '#2563eb');
    $buttonTextColor = $normalizeColor($sectionSettings['button_text_color'] ?? null, '#ffffff');
    $buttonBorderColor = $normalizeColor($sectionSettings['button_border_color'] ?? null, $buttonBackgroundColor);
    $cardBackgroundColor = $normalizeColor($sectionSettings['card_background_color'] ?? null, '#ffffff');
    $cardBorderColor = $normalizeColor($sectionSettings['card_border_color'] ?? null, '#ffffff');
    $cardHeadingColor = $normalizeColor($sectionSettings['card_heading_color'] ?? null, $headingColor);
    $cardTextColor = $normalizeColor($sectionSettings['card_text_color'] ?? null, $mutedTextColor);
    $paddingMap = ['none' => '0', 'compact' => '2rem 1rem', 'default' => '', 'spacious' => '6rem 1rem'];
    $marginMap = ['none' => '0', 'sm' => '1rem 0', 'md' => '2rem 0', 'lg' => '4rem 0'];
    $radiusMap = ['none' => '0', 'xl' => '1rem', '2xl' => '1.5rem', '3xl' => '1.75rem'];
    $gapMap = ['tight' => '0.75rem', 'default' => '1.5rem', 'loose' => '2rem', 'spacious' => '3rem'];
    $cardPaddingMap = ['compact' => '1rem', 'default' => '1.25rem', 'spacious' => '1.75rem'];
    $cardRadiusMap = ['none' => '0', 'xl' => '1rem', '2xl' => '1.5rem', '3xl' => '1.75rem'];
    $imageRadiusMap = ['none' => '0', 'xl' => '1rem', '2xl' => '1.5rem', '3xl' => '1.75rem'];
    $imageAspectMap = ['square' => '1 / 1', 'photo' => '4 / 3', 'video' => '16 / 9', 'wide' => '21 / 9', 'portrait' => '3 / 4'];
    $rawFilterStyle = $sectionSettings['filter_panel_style'] ?? 'solid';
    $filterStyle = in_array($rawFilterStyle, ['solid', 'soft', 'outline', 'plain'], true)
        ? $rawFilterStyle
        : 'solid';
    $rawAlign = $sectionSettings['text_align'] ?? 'inherit';
    $align = in_array($rawAlign, ['inherit', 'left', 'center', 'right'], true)
        ? $rawAlign
        : 'inherit';
    $padding = $paddingMap[$sectionSettings['spacing_padding'] ?? 'default'] ?? '';
    $margin = $marginMap[$sectionSettings['spacing_margin'] ?? 'none'] ?? '0';
    $radius = $radiusMap[$sectionSettings['border_radius'] ?? 'none'] ?? '0';
    $gap = $gapMap[$sectionSettings['section_gap'] ?? 'default'] ?? $gapMap['default'];
    $cardPadding = $cardPaddingMap[$sectionSettings['card_padding'] ?? 'default'] ?? $cardPaddingMap['default'];
    $cardRadius = $cardRadiusMap[$sectionSettings['card_radius'] ?? '3xl'] ?? $cardRadiusMap['3xl'];
    $imageRadius = $imageRadiusMap[$sectionSettings['image_radius'] ?? 'none'] ?? $imageRadiusMap['none'];
    $imageAspect = $imageAspectMap[$sectionSettings['image_aspect_ratio'] ?? 'photo'] ?? $imageAspectMap['photo'];
    $heroOverlayOpacity = max(0, min((int) ($sectionSettings['hero_overlay_opacity'] ?? 0), 90)) / 100;
    $heroOverlayColor = $normalizeColor($sectionSettings['hero_overlay_color'] ?? null, '#020617');
    $gridColumns = max(1, min((int) ($sectionSettings['grid_columns'] ?? ($sectionSettings['list_columns'] ?? 4)), 6));
    $listColumns = max(1, min((int) ($sectionSettings['list_columns'] ?? 4), 6));
    $statsColumns = max(1, min((int) ($sectionSettings['stats_columns'] ?? 4), 4));
    $galleryColumns = max(1, min((int) ($sectionSettings['gallery_columns'] ?? 3), 4));
    $filterBg = match ($filterStyle) {
        'plain' => 'transparent',
        'outline' => 'transparent',
        'soft' => "color-mix(in srgb, {$cardBackgroundColor} 10%, transparent)",
        default => "color-mix(in srgb, {$cardBackgroundColor} 18%, transparent)",
    };
    $filterBorder = $filterStyle === 'plain' ? 'transparent' : "color-mix(in srgb, {$cardBorderColor} 24%, transparent)";
    $buttonRadiusMap = ['lg' => '0.75rem', '2xl' => '1rem', 'full' => '9999px'];
    $buttonRadius = $buttonRadiusMap[$sectionSettings['button_radius'] ?? '2xl'] ?? $buttonRadiusMap['2xl'];
    $rawButtonStyle = $sectionSettings['button_style'] ?? 'solid';
    $buttonStyle = in_array($rawButtonStyle, ['solid', 'outline', 'ghost', 'glass'], true)
        ? $rawButtonStyle
        : 'solid';
    $layoutWidthMap = ['4xl' => '56rem', '5xl' => '64rem', '7xl' => '80rem', 'full' => '100%'];
    $layoutWidth = $layoutWidthMap[$sectionSettings['layout_width'] ?? '7xl'] ?? $layoutWidthMap['7xl'];
    $buttonVars = match ($buttonStyle) {
        'outline' => "--section-button-bg: transparent; --section-button-border: {$buttonBorderColor}; --section-button-color: {$buttonTextColor};",
        'ghost' => "--section-button-bg: transparent; --section-button-border: transparent; --section-button-color: {$buttonTextColor};",
        'glass' => "--section-button-bg: color-mix(in srgb, {$buttonBackgroundColor} 18%, transparent); --section-button-border: {$buttonBorderColor}; --section-button-color: {$buttonTextColor};",
        default => "--section-button-bg: {$buttonBackgroundColor}; --section-button-border: {$buttonBorderColor}; --section-button-color: {$buttonTextColor};",
    };

    echo $useGradient
        ? "background: linear-gradient({$direction}, {$startColor}, {$endColor});"
        : "background-color: {$startColor};";

    echo " color: {$textColor}; margin: {$margin}; border-radius: {$radius}; --section-text-color: {$textColor}; --section-heading-color: {$headingColor}; --section-muted-color: {$mutedTextColor}; --section-accent-color: {$accentColor}; --section-gap: {$gap}; --section-card-bg: color-mix(in srgb, {$cardBackgroundColor} 7%, transparent); --section-card-border: color-mix(in srgb, {$cardBorderColor} 22%, transparent); --section-card-heading-color: {$cardHeadingColor}; --section-card-text-color: {$cardTextColor}; --section-card-padding: {$cardPadding}; --section-card-radius: {$cardRadius}; --section-image-radius: {$imageRadius}; --section-image-aspect: {$imageAspect}; --section-filter-bg: {$filterBg}; --section-filter-border: {$filterBorder}; --section-hero-overlay-color: {$heroOverlayColor}; --section-hero-overlay-opacity: {$heroOverlayOpacity}; --section-grid-columns: {$gridColumns}; --section-list-columns: {$listColumns}; --section-stats-columns: {$statsColumns}; --section-gallery-columns: {$galleryColumns}; --section-button-radius: {$buttonRadius}; --section-layout-width: {$layoutWidth}; {$buttonVars}";

    if ($align !== 'inherit') {
        echo " text-align: {$align};";
    }

    if ($padding !== '') {
        echo " padding: {$padding};";
    }
@endphp
