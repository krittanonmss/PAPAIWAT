<script>
    document.addEventListener('DOMContentLoaded', () => {
        const currentForm = () => document.querySelector('[data-ajax-list-form]');
        const currentResults = () => document.querySelector('[data-ajax-list-results]');
        const form = currentForm();
        const results = currentResults();

        if (!form || !results) {
            return;
        }

        let controller = null;
        let inputTimer = null;
        let isResetting = false;

        const buildUrl = (baseUrl = currentForm()?.action) => {
            const form = currentForm();
            const url = new URL(baseUrl, window.location.origin);
            const formData = new FormData(form);

            url.search = '';

            for (const [key, value] of formData.entries()) {
                if (String(value).trim() !== '') {
                    url.searchParams.append(key, value);
                }
            }

            return url;
        };

        const loadList = async (url, pushState = true) => {
            const form = currentForm();
            const results = currentResults();

            if (!form || !results) {
                window.location.href = url.toString();
                return;
            }

            if (controller) {
                controller.abort();
            }

            controller = new AbortController();
            results.classList.add('opacity-60', 'pointer-events-none');
            form.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.disabled = true;
                button.classList.add('opacity-60');
            });

            try {
                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nextResults = doc.querySelector('[data-ajax-list-results]');

                if (!nextResults) {
                    throw new Error('Missing results fragment');
                }

                results.innerHTML = nextResults.innerHTML;
                window.Alpine?.initTree?.(results);

                if (pushState) {
                    window.history.pushState({}, '', url.toString());
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    window.location.href = url.toString();
                }
            } finally {
                results.classList.remove('opacity-60', 'pointer-events-none');
                form.querySelectorAll('button[type="submit"]').forEach((button) => {
                    button.disabled = false;
                    button.classList.remove('opacity-60');
                });
            }
        };

        document.addEventListener('submit', (event) => {
            if (!event.target.matches('[data-ajax-list-form]')) {
                return;
            }

            event.preventDefault();
            loadList(buildUrl());
        });

        document.addEventListener('change', (event) => {
            const form = event.target.closest('[data-ajax-list-form]');

            if (!form) {
                return;
            }

            if (isResetting) {
                return;
            }

            if (event.target.matches('select, input[type="checkbox"], input[type="radio"], input[type="date"], input[type="hidden"]')) {
                loadList(buildUrl());
            }
        });

        document.addEventListener('input', (event) => {
            const form = event.target.closest('[data-ajax-list-form]');

            if (!form) {
                return;
            }

            if (!event.target.matches('input[type="text"], input[type="search"]')) {
                return;
            }

            clearTimeout(inputTimer);
            inputTimer = setTimeout(() => loadList(buildUrl()), 450);
        });

        document.addEventListener('click', (event) => {
            const resetLink = event.target.closest('[data-ajax-list-reset]');
            const pageLink = event.target.closest('[data-ajax-list-results] a[href]');

            if (resetLink) {
                event.preventDefault();
                const form = currentForm();

                if (!form) {
                    loadList(new URL(resetLink.href, window.location.origin));
                    return;
                }

                isResetting = true;
                form.reset();
                form.querySelectorAll('input[type="hidden"]').forEach((input) => {
                    input.value = '';
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
                isResetting = false;
                loadList(new URL(resetLink.href, window.location.origin));
                return;
            }

            if (pageLink && new URL(pageLink.href, window.location.origin).pathname === window.location.pathname) {
                event.preventDefault();
                loadList(new URL(pageLink.href));
            }
        });

        window.addEventListener('popstate', () => {
            loadList(new URL(window.location.href), false);
        });
    });
</script>
