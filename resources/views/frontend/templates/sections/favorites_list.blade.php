@php
    $content = $section->content_data ?? [];
    $copy = [
        'empty_title' => trim((string) ($content['empty_title'] ?? '')) ?: 'ยังไม่มีรายการโปรด',
        'empty_subtitle' => trim((string) ($content['empty_subtitle'] ?? '')) ?: 'กดปุ่มหัวใจในหน้าวัดหรือบทความเพื่อเพิ่มรายการโปรด',
        'temple_eyebrow' => trim((string) ($content['temple_eyebrow'] ?? '')) ?: 'Temples',
        'temple_title' => trim((string) ($content['temple_title'] ?? '')) ?: 'วัดที่บันทึกไว้',
        'temple_card_label' => trim((string) ($content['temple_card_label'] ?? '')) ?: 'วัด',
        'article_eyebrow' => trim((string) ($content['article_eyebrow'] ?? '')) ?: 'Articles',
        'article_title' => trim((string) ($content['article_title'] ?? '')) ?: 'บทความที่บันทึกไว้',
        'article_card_label' => trim((string) ($content['article_card_label'] ?? '')) ?: 'บทความ',
        'section_count_suffix' => trim((string) ($content['section_count_suffix'] ?? '')) ?: 'รายการ',
        'favorite_count_suffix' => trim((string) ($content['favorite_count_suffix'] ?? '')) ?: 'รายการโปรด',
        'open_label' => trim((string) ($content['open_label'] ?? '')) ?: 'เปิดดู',
        'remove_label' => trim((string) ($content['remove_label'] ?? '')) ?: 'ลบ',
    ];
@endphp
<section class="px-4 py-12 text-white md:py-16" style="@include('frontend.templates.sections._background')" data-favorites-root>
    <div class="mx-auto max-w-7xl">
        <script type="application/json" data-favorites-copy>@json($copy, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)</script>

        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30">
            @if(!empty($content['eyebrow']))
                <p class="text-sm font-medium text-amber-300">{{ $content['eyebrow'] }}</p>
            @endif
            <h1 class="mt-2 text-3xl font-bold">{{ $content['title'] ?? 'รายการโปรดของฉัน' }}</h1>
            @if(!empty($content['subtitle']))
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-400">{{ $content['subtitle'] }}</p>
            @endif
        </div>

        <div data-favorites-empty class="mt-6 hidden rounded-3xl border border-white/10 bg-white/[0.04] p-10 text-center">
            <p class="text-lg font-semibold text-white">{{ $copy['empty_title'] }}</p>
            <p class="mt-2 text-sm text-slate-500">{{ $copy['empty_subtitle'] }}</p>
        </div>

        <div data-favorites-section="temple" class="mt-8 hidden">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-blue-300">{{ $copy['temple_eyebrow'] }}</p>
                    <h2 class="mt-1 text-2xl font-bold text-white">{{ $copy['temple_title'] }}</h2>
                </div>
                <span data-favorites-count="temple" class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-slate-400"></span>
            </div>
            <div data-favorites-list="temple" class="grid items-stretch gap-4 md:grid-cols-2 xl:grid-cols-3"></div>
        </div>

        <div data-favorites-section="article" class="mt-10 hidden">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-amber-300">{{ $copy['article_eyebrow'] }}</p>
                    <h2 class="mt-1 text-2xl font-bold text-white">{{ $copy['article_title'] }}</h2>
                </div>
                <span data-favorites-count="article" class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-slate-400"></span>
            </div>
            <div data-favorites-list="article" class="grid items-stretch gap-4 md:grid-cols-2 xl:grid-cols-3"></div>
        </div>
    </div>
</section>

@once
    <script>
        (() => {
            const storageKey = 'papaiwat_favorites';
            const itemsEndpoint = @json(route('favorites.items'));
            const favoriteEndpoint = @json(route('interactions.favorite'));
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

            const syncFavoriteCount = async (item, action) => {
                await fetch(favoriteEndpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        type: item.type,
                        id: item.id,
                        action,
                    }),
                });
            };

            const hydrateFavorites = async (items) => {
                if (!items.length) {
                    return items;
                }

                try {
                    const response = await fetch(itemsEndpoint, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            items: items.map((item) => ({
                                type: item.type,
                                id: item.id,
                            })),
                        }),
                    });

                    if (!response.ok) {
                        return items;
                    }

                    const data = await response.json();
                    const serverItems = new Map((data.items || []).map((item) => [itemKey(item), item]));

                    return items.map((item) => ({
                        ...item,
                        ...(serverItems.get(itemKey(item)) || {}),
                    }));
                } catch (error) {
                    return items;
                }
            };

            const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char]));

            const defaultCopy = {
                temple_card_label: 'วัด',
                article_card_label: 'บทความ',
                section_count_suffix: 'รายการ',
                favorite_count_suffix: 'รายการโปรด',
                open_label: 'เปิดดู',
                remove_label: 'ลบ',
            };

            const readCopy = (root) => {
                try {
                    const script = root.querySelector('[data-favorites-copy]');
                    return {
                        ...defaultCopy,
                        ...(script ? JSON.parse(script.textContent || '{}') : {}),
                    };
                } catch (error) {
                    return defaultCopy;
                }
            };

            const cardHtml = (item, copy) => {
                const key = itemKey(item);
                const typeLabel = item.type === 'temple' ? copy.temple_card_label : copy.article_card_label;

                return `
                    <article class="flex h-full min-h-[30rem] flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20">
                        ${item.image ? `<img src="${escapeHtml(item.image)}" alt="" class="aspect-[16/9] w-full flex-none object-cover">` : `<div class="aspect-[16/9] w-full flex-none bg-slate-900"></div>`}
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex min-h-8 items-center justify-between gap-3">
                                <span class="rounded-full border border-white/10 px-2.5 py-1 text-xs text-slate-400">${escapeHtml(typeLabel)}</span>
                                <span class="shrink-0 text-xs text-red-200">${Number(item.count || 0).toLocaleString()} ${escapeHtml(copy.favorite_count_suffix)}</span>
                            </div>
                            <h3 class="mt-3 line-clamp-2 text-lg font-semibold leading-7 text-white">${escapeHtml(item.title)}</h3>
                            <p class="mt-2 line-clamp-4 min-h-[6rem] text-sm leading-6 text-slate-400">${escapeHtml(item.excerpt || '')}</p>
                            <div class="mt-auto flex gap-2 pt-5">
                                <a href="${escapeHtml(item.url)}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">${escapeHtml(copy.open_label)}</a>
                                <button type="button" data-remove-favorite="${escapeHtml(key)}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/10">${escapeHtml(copy.remove_label)}</button>
                            </div>
                        </div>
                    </article>
                `;
            };

            const renderRoot = async (root) => {
                const copy = readCopy(root);
                const items = await hydrateFavorites(readFavorites());
                const groups = {
                    temple: items.filter((item) => item.type === 'temple'),
                    article: items.filter((item) => item.type === 'article'),
                };

                root.querySelector('[data-favorites-empty]')?.classList.toggle('hidden', items.length > 0);

                Object.entries(groups).forEach(([type, typeItems]) => {
                    const section = root.querySelector(`[data-favorites-section="${type}"]`);
                    const list = root.querySelector(`[data-favorites-list="${type}"]`);
                    const count = root.querySelector(`[data-favorites-count="${type}"]`);

                    if (!section || !list) {
                        return;
                    }

                    section.classList.toggle('hidden', typeItems.length === 0);
                    list.innerHTML = typeItems.map((item) => cardHtml(item, copy)).join('');

                    if (count) {
                        count.textContent = `${typeItems.length.toLocaleString()} ${copy.section_count_suffix}`;
                    }
                });

                root.querySelectorAll('[data-remove-favorite]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const key = button.dataset.removeFavorite;
                        const item = readFavorites().find((favorite) => itemKey(favorite) === key);
                        writeFavorites(readFavorites().filter((favorite) => itemKey(favorite) !== key));
                        if (item) {
                            syncFavoriteCount(item, 'remove').catch(() => {});
                        }
                        renderAll();
                    });
                });
            };

            const renderAll = () => {
                document.querySelectorAll('[data-favorites-root]').forEach((root) => {
                    renderRoot(root);
                });
            };

            window.addEventListener('storage', (event) => {
                if (event.key === storageKey) {
                    renderAll();
                }
            });
            window.addEventListener('papaiwat:favorites-updated', renderAll);

            renderAll();
        })();
    </script>
@endonce
