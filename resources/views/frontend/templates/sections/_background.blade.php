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

    echo $useGradient
        ? "background: linear-gradient({$direction}, {$startColor}, {$endColor});"
        : "background-color: {$startColor};";
@endphp
