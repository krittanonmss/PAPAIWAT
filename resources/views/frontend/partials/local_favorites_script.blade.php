<script>
    (() => {
        const storageKey = 'papaiwat_favorites';
        const endpoint = @json(route('interactions.favorite'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const readFavorites = () => {
            try {
                const value = JSON.parse(localStorage.getItem(storageKey) || '[]');
                return Array.isArray(value) ? value : [];
            } catch (error) {
                return [];
            }
        };

        const writeFavorites = (items) => {
            localStorage.setItem(storageKey, JSON.stringify(items));
        };

        const itemKey = (item) => `${item.type}:${Number(item.id)}`;

        const currentCount = (type, id) => {
            const element = document.querySelector(`[data-favorite-count="${type}:${id}"]`);

            return Number((element?.textContent || '0').replace(/,/g, '')) || 0;
        };

        const updateCount = (type, id, count) => {
            document.querySelectorAll(`[data-favorite-count="${type}:${id}"]`).forEach((element) => {
                element.textContent = Number(count || 0).toLocaleString();
            });
        };

        const syncFavoriteCount = async (payload, action) => {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    type: payload.type,
                    id: payload.id,
                    action,
                }),
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            updateCount(payload.type, payload.id, data.count);
        };

        const updateButton = (button, isSaved) => {
            button.style.backgroundColor = isSaved ? '#ef4444' : '';
            button.style.color = isSaved ? '#ffffff' : '';
            button.classList.toggle('bg-red-500', isSaved);
            button.classList.toggle('text-white', isSaved);
            button.classList.toggle('shadow-lg', isSaved);
            button.classList.toggle('shadow-red-500/25', isSaved);
            button.classList.toggle('bg-white/10', !isSaved);
            button.classList.toggle('hover:bg-white/15', !isSaved);
            button.classList.toggle('is-favorite-saved', isSaved);
            button.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
            button.querySelectorAll('[data-favorite-saved]').forEach((element) => element.classList.toggle('hidden', !isSaved));
            button.querySelectorAll('[data-favorite-unsaved]').forEach((element) => element.classList.toggle('hidden', isSaved));
            button.querySelectorAll('[data-favorite-icon]').forEach((element) => {
                element.setAttribute('fill', isSaved ? 'currentColor' : 'none');
            });
        };

        const pulseButton = (button) => {
            button.classList.remove('favorite-pop');
            void button.offsetWidth;
            button.classList.add('favorite-pop');
        };

        const syncMatchingButtons = (payload, isSaved) => {
            const key = itemKey(payload);

            document.querySelectorAll('[data-local-favorite-toggle]').forEach((button) => {
                try {
                    const buttonPayload = JSON.parse(button.dataset.favorite || '{}');

                    if (itemKey(buttonPayload) === key) {
                        updateButton(button, isSaved);
                        pulseButton(button);
                    }
                } catch (error) {
                    //
                }
            });
        };

        const init = () => {
            if (!document.getElementById('local-favorite-style')) {
                const style = document.createElement('style');
                style.id = 'local-favorite-style';
                style.textContent = `
                    @keyframes papaiwatFavoritePop {
                        0% { transform: scale(1); }
                        45% { transform: scale(1.08); }
                        100% { transform: scale(1); }
                    }

                    .favorite-pop {
                        animation: papaiwatFavoritePop 280ms ease-out;
                    }

                    .is-favorite-saved [data-favorite-icon] {
                        color: currentColor;
                    }
                `;
                document.head.appendChild(style);
            }

            document.querySelectorAll('[data-local-favorite-toggle]').forEach((button) => {
                if (button.dataset.favoriteReady === '1') {
                    return;
                }

                button.dataset.favoriteReady = '1';

                let payload = {};

                try {
                    payload = JSON.parse(button.dataset.favorite || '{}');
                } catch (error) {
                    return;
                }

                if (!payload.type || !payload.id) {
                    return;
                }

                const key = itemKey(payload);
                const isSaved = () => readFavorites().some((item) => itemKey(item) === key);

                updateButton(button, isSaved());

                button.addEventListener('click', () => {
                    const items = readFavorites();
                    const existingIndex = items.findIndex((item) => itemKey(item) === key);

                    if (existingIndex >= 0) {
                        items.splice(existingIndex, 1);
                        writeFavorites(items);
                        syncMatchingButtons(payload, false);
                        updateCount(payload.type, payload.id, Math.max(currentCount(payload.type, payload.id) - 1, 0));
                        syncFavoriteCount(payload, 'remove').catch(() => {});
                        window.dispatchEvent(new CustomEvent('papaiwat:favorites-updated'));
                        return;
                    }

                    items.unshift({
                        ...payload,
                        addedAt: new Date().toISOString(),
                    });
                    writeFavorites(items);
                    syncMatchingButtons(payload, true);
                    updateCount(payload.type, payload.id, currentCount(payload.type, payload.id) + 1);
                    syncFavoriteCount(payload, 'add').catch(() => {});
                    window.dispatchEvent(new CustomEvent('papaiwat:favorites-updated'));
                });
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
