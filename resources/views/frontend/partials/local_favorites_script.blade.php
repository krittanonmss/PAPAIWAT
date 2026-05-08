<script>
    (() => {
        const storageKey = 'papaiwat_favorites';

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

        const itemKey = (item) => `${item.type}:${item.id}`;

        const updateButton = (button, isSaved) => {
            button.classList.toggle('bg-amber-400', isSaved);
            button.classList.toggle('text-slate-950', isSaved);
            button.classList.toggle('bg-white/10', !isSaved);
            button.classList.toggle('text-white', !isSaved);
            button.classList.toggle('hover:bg-white/15', !isSaved);
            button.querySelectorAll('[data-favorite-saved]').forEach((element) => element.classList.toggle('hidden', !isSaved));
            button.querySelectorAll('[data-favorite-unsaved]').forEach((element) => element.classList.toggle('hidden', isSaved));
            button.querySelectorAll('[data-favorite-icon]').forEach((element) => {
                element.setAttribute('fill', isSaved ? 'currentColor' : 'none');
            });
        };

        const init = () => {
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
                        updateButton(button, false);
                        return;
                    }

                    items.unshift({
                        ...payload,
                        addedAt: new Date().toISOString(),
                    });
                    writeFavorites(items);
                    updateButton(button, true);
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
