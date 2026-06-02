<!DOCTYPE html>
@php
    $frontendSiteSettings = $frontendSiteSettings ?? \App\Support\SiteSettings::all();
    $siteName = $frontendSiteSettings['general']['site_name'] ?? 'PAPAIWAT';
    $siteTagline = $frontendSiteSettings['general']['tagline'] ?? null;
    $siteLocale = $frontendSiteSettings['general']['locale'] ?? app()->getLocale();
    $seoDefaultTitle = $frontendSiteSettings['seo']['default_title'] ?? null;
    $seoDefaultDescription = $frontendSiteSettings['seo']['default_description'] ?? null;
    $resolvedDefaultTitle = ($seoDefaultTitle && !($seoDefaultTitle === 'PAPAIWAT' && $siteName !== 'PAPAIWAT'))
        ? $seoDefaultTitle
        : trim($siteName.($siteTagline ? ' | '.$siteTagline : ''));
    $resolvedDefaultDescription = ($seoDefaultDescription && !($seoDefaultDescription === 'PAPAIWAT Platform' && $siteTagline))
        ? $seoDefaultDescription
        : ($siteTagline ?: ($seoDefaultDescription ?: $siteName));
    $tagManagerContainerId = $frontendSiteSettings['integrations']['tag_manager_container_id'] ?? null;
    $mapsEnabled = (bool) ($frontendSiteSettings['integrations']['maps_enabled'] ?? false);
    $mapsPublicBrowserKey = $frontendSiteSettings['integrations']['maps_public_browser_key'] ?? null;
@endphp
<html lang="{{ $siteLocale }}">
<head>
    @php
        $pageOgImage = ($page ?? null)?->ogImage ?? null;
        $resolvedOgImage = $pageOgImage ?: ($frontendOgImage ?? null);
        $resolvedOgImageUrl = $resolvedOgImage?->path
            ? (filter_var($resolvedOgImage->path, FILTER_VALIDATE_URL)
                ? $resolvedOgImage->path
                : url(\Illuminate\Support\Facades\Storage::url($resolvedOgImage->path)))
            : null;
        $resolvedCanonicalUrl = ($page ?? null)?->canonical_url
            ?: (($frontendSiteSettings['seo']['canonical_base_url'] ?? null)
                ? rtrim($frontendSiteSettings['seo']['canonical_base_url'], '/') . request()->getPathInfo()
                : null);
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? $resolvedDefaultTitle)</title>
    <meta name="description" content="@yield('meta_description', $metaDescription ?? $resolvedDefaultDescription)">
    <meta name="robots" content="{{ ($frontendSiteSettings['seo']['indexing_enabled'] ?? true) ? 'index,follow' : 'noindex,nofollow' }}">
    @if ($resolvedCanonicalUrl)
        <link rel="canonical" href="{{ $resolvedCanonicalUrl }}">
    @endif
    @if ($resolvedOgImageUrl)
        <meta property="og:image" content="{{ $resolvedOgImageUrl }}">
        <meta name="twitter:image" content="{{ $resolvedOgImageUrl }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if ($tagManagerContainerId)
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer',@js($tagManagerContainerId));
        </script>
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --frontend-zoom: 0.8;
        }

        body {
            zoom: var(--frontend-zoom);
            font-weight: 300;
        }

        .font-medium {
            font-weight: 400 !important;
        }

        .font-semibold {
            font-weight: 500 !important;
        }

        .font-bold {
            font-weight: 600 !important;
        }

        .text-slate-400,
        .text-gray-400,
        .text-zinc-400 {
            color: rgb(203 213 225) !important;
        }

        .text-slate-500,
        .text-gray-500,
        .text-zinc-500 {
            color: rgb(148 163 184) !important;
        }

        [data-section-filter-root][data-section-loading="true"] {
            opacity: 0.65;
            pointer-events: none;
            transition: opacity 150ms ease;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    @if ($tagManagerContainerId)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $tagManagerContainerId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    @include('frontend.partials.header')

    @if (($frontendSiteSettings['maintenance']['announcement_enabled'] ?? false) && ($frontendSiteSettings['maintenance']['announcement_text'] ?? null))
        @php
            $announcementClass = match ($frontendSiteSettings['maintenance']['announcement_level'] ?? 'info') {
                'critical' => 'bg-red-700 text-white',
                'warning' => 'bg-amber-500 text-slate-950',
                default => 'bg-blue-700 text-white',
            };
        @endphp
        <div class="{{ $announcementClass }} px-5 py-3 text-center text-sm font-medium">{{ $frontendSiteSettings['maintenance']['announcement_text'] }}</div>
    @endif

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('frontend.partials.footer')
    @if ($frontendSiteSettings['integrations']['analytics_measurement_id'] ?? null)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $frontendSiteSettings['integrations']['analytics_measurement_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @js($frontendSiteSettings['integrations']['analytics_measurement_id']));
        </script>
    @endif
    @if ($mapsEnabled && $mapsPublicBrowserKey)
        <script>
            window.papaiwatMaps = {
                enabled: true,
                publicKey: @js($mapsPublicBrowserKey),
                locale: @js($siteLocale),
            };
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($mapsPublicBrowserKey) }}&language={{ urlencode($siteLocale) }}"></script>
    @endif
    <script>
        (() => {
            if (window.papaiwatSectionFiltersReady) {
                return;
            }

            window.papaiwatSectionFiltersReady = true;

            const rootSelector = '[data-section-filter-root]';
            const linkSelector = '[data-section-filter-link], [data-section-filter-pagination] a';

            const getRoot = (element) => element?.closest(rootSelector);

            const buildFormUrl = (form) => {
                const actionUrl = new URL(form.getAttribute('action') || window.location.href, window.location.href);
                const url = new URL(window.location.href);
                const formData = new FormData(form);

                url.pathname = actionUrl.pathname;
                url.hash = actionUrl.hash;

                Array.from(form.elements).forEach((field) => {
                    if (field.name) {
                        url.searchParams.delete(field.name);
                    }
                });

                if (form.dataset.sectionPageKey) {
                    url.searchParams.delete(form.dataset.sectionPageKey);
                }

                formData.forEach((value, key) => {
                    const normalized = String(value).trim();

                    if (normalized !== '') {
                        url.searchParams.set(key, normalized);
                    }
                });

                return url;
            };

            const replaceSection = async (root, url) => {
                if (!root?.id) {
                    window.location.href = url.toString();
                    return;
                }

                root.setAttribute('data-section-loading', 'true');
                root.setAttribute('aria-busy', 'true');

                try {
                    const response = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'text/html',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`Request failed: ${response.status}`);
                    }

                    const html = await response.text();
                    const documentFragment = new DOMParser().parseFromString(html, 'text/html');
                    const nextRoot = documentFragment.getElementById(root.id);

                    if (!nextRoot) {
                        throw new Error('Section root was not found in the response.');
                    }

                    root.replaceWith(nextRoot);
                    window.history.pushState({ sectionFilter: root.id }, '', url.toString());
                } catch (error) {
                    window.location.href = url.toString();
                }
            };

            document.addEventListener('submit', (event) => {
                const form = event.target.closest('[data-section-filter-form]');

                if (!form) {
                    return;
                }

                const root = getRoot(form);

                if (!root) {
                    return;
                }

                event.preventDefault();
                replaceSection(root, buildFormUrl(form));
            });

            document.addEventListener('click', (event) => {
                const link = event.target.closest(linkSelector);

                if (!link) {
                    return;
                }

                const root = getRoot(link);

                if (!root) {
                    return;
                }

                const url = new URL(link.href, window.location.href);

                if (url.origin !== window.location.origin) {
                    return;
                }

                event.preventDefault();
                replaceSection(root, url);
            });

            window.addEventListener('popstate', () => {
                window.location.reload();
            });
        })();

        (() => {
            if (window.papaiwatSectionSlidersReady) {
                return;
            }

            window.papaiwatSectionSlidersReady = true;

            const sliderSelector = '[data-section-slider]';
            const trackSelector = '[data-section-slider-track]';
            const intervalMs = 4500;
            const scrollDurationMs = 520;

            const scrollAmount = (track) => {
                const firstCard = track.firstElementChild;

                if (!firstCard) {
                    return Math.max(track.clientWidth * 0.85, 280);
                }

                const styles = window.getComputedStyle(track);
                const gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;

                return firstCard.getBoundingClientRect().width + gap;
            };

            const finishAfterScroll = (track, callback) => {
                window.clearTimeout(Number(track.dataset.sliderFinishTimer || 0));
                track.dataset.sliderFinishTimer = String(window.setTimeout(() => {
                    callback();
                    track.dataset.sliderMoving = 'false';
                }, scrollDurationMs));
            };

            const scrollSlider = (track, direction = 1) => {
                if (track.dataset.sliderMoving === 'true' || track.children.length <= 1) {
                    return;
                }

                track.dataset.sliderMoving = 'true';
                const amount = scrollAmount(track);

                if (direction > 0) {
                    track.scrollBy({
                        left: amount,
                        behavior: 'smooth',
                    });

                    finishAfterScroll(track, () => {
                        const firstSlide = track.firstElementChild;

                        if (firstSlide) {
                            track.appendChild(firstSlide);
                            track.scrollLeft -= amount;
                        }
                    });

                    return;
                }

                const lastSlide = track.lastElementChild;

                if (lastSlide) {
                    track.insertBefore(lastSlide, track.firstElementChild);
                    track.scrollLeft += amount;
                }

                track.scrollBy({
                    left: -amount,
                    behavior: 'smooth',
                });

                finishAfterScroll(track, () => {});
            };

            const initSlider = (slider) => {
                if (slider.dataset.sliderBound === 'true') {
                    return;
                }

                const track = slider.querySelector(trackSelector);

                if (!track) {
                    return;
                }

                slider.dataset.sliderBound = 'true';

                slider.querySelectorAll('[data-section-slider-prev]').forEach((button) => {
                    button.addEventListener('click', () => scrollSlider(track, -1));
                });

                slider.querySelectorAll('[data-section-slider-next]').forEach((button) => {
                    button.addEventListener('click', () => scrollSlider(track, 1));
                });

                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    return;
                }

                let isPaused = false;

                slider.addEventListener('mouseenter', () => {
                    isPaused = true;
                });
                slider.addEventListener('mouseleave', () => {
                    isPaused = false;
                });
                slider.addEventListener('focusin', () => {
                    isPaused = true;
                });
                slider.addEventListener('focusout', () => {
                    isPaused = false;
                });

                window.setInterval(() => {
                    if (!document.body.contains(slider) || isPaused || document.hidden) {
                        return;
                    }

                    scrollSlider(track, 1);
                }, intervalMs);
            };

            const initSliders = () => {
                document.querySelectorAll(sliderSelector).forEach(initSlider);
            };

            document.addEventListener('DOMContentLoaded', initSliders);

            new MutationObserver(initSliders).observe(document.body, {
                childList: true,
                subtree: true,
            });
        })();
    </script>
</body>
</html>
