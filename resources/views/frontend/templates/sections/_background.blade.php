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
    $fontSizeMap = ['sm' => '0.875rem', 'base' => '1rem', 'lg' => '1.125rem', 'xl' => '1.25rem'];
    $fontWeightMap = ['normal' => '400', 'medium' => '500', 'semibold' => '600', 'bold' => '700'];
    $paddingMap = ['none' => '0', 'compact' => '2rem 1rem', 'default' => '', 'spacious' => '6rem 1rem'];
    $marginMap = ['none' => '0', 'sm' => '1rem 0', 'md' => '2rem 0', 'lg' => '4rem 0'];
    $radiusMap = ['none' => '0', 'xl' => '1rem', '2xl' => '1.5rem', '3xl' => '1.75rem'];
    $rawAlign = $sectionSettings['text_align'] ?? 'inherit';
    $align = in_array($rawAlign, ['inherit', 'left', 'center', 'right'], true)
        ? $rawAlign
        : 'inherit';
    $fontSize = $fontSizeMap[$sectionSettings['font_size'] ?? 'base'] ?? $fontSizeMap['base'];
    $fontWeight = $fontWeightMap[$sectionSettings['font_weight'] ?? 'normal'] ?? $fontWeightMap['normal'];
    $padding = $paddingMap[$sectionSettings['spacing_padding'] ?? 'default'] ?? '';
    $margin = $marginMap[$sectionSettings['spacing_margin'] ?? 'none'] ?? '0';
    $radius = $radiusMap[$sectionSettings['border_radius'] ?? 'none'] ?? '0';
    $buttonRadiusMap = ['lg' => '0.75rem', '2xl' => '1rem', 'full' => '9999px'];
    $buttonRadius = $buttonRadiusMap[$sectionSettings['button_radius'] ?? '2xl'] ?? $buttonRadiusMap['2xl'];
    $rawButtonStyle = $sectionSettings['button_style'] ?? 'solid';
    $buttonStyle = in_array($rawButtonStyle, ['solid', 'outline', 'ghost', 'glass'], true)
        ? $rawButtonStyle
        : 'solid';
    $layoutWidthMap = ['4xl' => '56rem', '5xl' => '64rem', '7xl' => '80rem', 'full' => '100%'];
    $layoutWidth = $layoutWidthMap[$sectionSettings['layout_width'] ?? '7xl'] ?? $layoutWidthMap['7xl'];
    $buttonVars = match ($buttonStyle) {
        'outline' => '--section-button-bg: transparent; --section-button-border: rgb(255 255 255 / 0.22); --section-button-color: #ffffff;',
        'ghost' => '--section-button-bg: transparent; --section-button-border: transparent; --section-button-color: #ffffff;',
        'glass' => '--section-button-bg: rgb(255 255 255 / 0.08); --section-button-border: rgb(255 255 255 / 0.12); --section-button-color: #ffffff;',
        default => '--section-button-bg: rgb(37 99 235); --section-button-border: transparent; --section-button-color: #ffffff;',
    };

    echo $useGradient
        ? "background: linear-gradient({$direction}, {$startColor}, {$endColor});"
        : "background-color: {$startColor};";

    echo " color: {$textColor}; font-size: {$fontSize}; font-weight: {$fontWeight}; margin: {$margin}; border-radius: {$radius}; --section-button-radius: {$buttonRadius}; --section-layout-width: {$layoutWidth}; {$buttonVars}";

    if ($align !== 'inherit') {
        echo " text-align: {$align};";
    }

    if ($padding !== '') {
        echo " padding: {$padding};";
    }
@endphp
