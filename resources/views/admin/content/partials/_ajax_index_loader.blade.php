<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-ajax-list-form]');
        const results = document.querySelector('[data-ajax-list-results]');

        if (!form || !results) {
            return;
        }

        let controller = null;
        let inputTimer = null;
        let isResetting = false;

        const buildUrl = (baseUrl = form.action) => {
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

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            loadList(buildUrl());
        });

        form.addEventListener('change', (event) => {
            if (isResetting) {
                return;
            }

            if (event.target.matches('select, input[type="checkbox"], input[type="radio"], input[type="date"], input[type="hidden"]')) {
                loadList(buildUrl());
            }
        });

        form.addEventListener('input', (event) => {
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

            if (pageLink && pageLink.href.includes(window.location.pathname)) {
                event.preventDefault();
                loadList(new URL(pageLink.href));
            }
        });

        window.addEventListener('popstate', () => {
            loadList(new URL(window.location.href), false);
        });
    });
</script>
