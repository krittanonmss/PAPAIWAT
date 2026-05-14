<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? 'PAPAIWAT')</title>
    <meta name="description" content="@yield('meta_description', $metaDescription ?? 'PAPAIWAT Platform')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    @include('frontend.partials.header')

    <main class="min-h-screen">
        @yield('content')
    </main>

    @include('frontend.partials.footer')
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
                const url = new URL(form.getAttribute('action') || window.location.href, window.location.href);
                const formData = new FormData(form);

                url.search = '';

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
