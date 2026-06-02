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
        'total_label' => trim((string) ($content['total_label'] ?? '')) ?: 'ทั้งหมด',
        'empty_image_text' => trim((string) ($content['empty_image_text'] ?? '')) ?: 'No Image',
    ];
    $canSyncUserFavorites = auth()->check()
        && auth()->user()?->hasVerifiedEmail()
        && \Illuminate\Support\Facades\Schema::hasTable('user_favorites');
    $accountFavorites = $canSyncUserFavorites
        ? \App\Models\UserFavorite::query()
            ->where('user_id', auth()->id())
            ->latest('added_at')
            ->limit(100)
            ->get()
            ->map(fn ($favorite) => [
                'type' => $favorite->favoritable_type,
                'id' => (int) $favorite->favoritable_id,
                'addedAt' => $favorite->added_at?->toISOString(),
            ])
            ->values()
        : collect();
@endphp
<section class="px-4 py-12 text-white md:py-20" style="@include('frontend.templates.sections._background')" data-favorites-root>
    <div class="mx-auto max-w-7xl">
        <script type="application/json" data-favorites-copy>@json($copy, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)</script>
        <script type="application/json" data-account-favorites>@json($accountFavorites, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)</script>

        <div data-section-card class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/20 backdrop-blur">
            <div data-section-card-padding class="grid gap-6 p-5 sm:p-6 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,24rem)] lg:items-end">
                <div class="min-w-0 self-center">
                    <div>
                        <p data-section-accent class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-300">{{ $content['eyebrow'] ?? 'รายการที่บันทึกไว้' }}</p>

                        <h1 data-section-heading class="mt-3 max-w-3xl text-3xl font-semibold leading-[1.18] text-white md:text-4xl">{{ $content['title'] ?? 'รายการโปรดของฉัน' }}</h1>
                        @if(!empty($content['subtitle']))
                            <p data-section-card-copy class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">{{ $content['subtitle'] }}</p>
                        @endif
                    </div>

                    @if(!empty($content['eyebrow']))
                        <span class="sr-only">{{ $content['eyebrow'] }}</span>
                    @endif

                    @if (session('success'))
                        <div class="mt-5 rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
                    @endif

                    @guest
                        <div class="mt-5 rounded-2xl border border-blue-400/20 bg-blue-500/10 px-4 py-4 text-sm text-blue-100">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="max-w-xl leading-7">รายการโปรดจะเก็บใน browser นี้ หากต้องการใช้ข้ามอุปกรณ์ให้สมัครสมาชิกและยืนยันอีเมล</p>
                                <div class="flex shrink-0 gap-2">
                                    <a href="{{ route('login', ['redirect' => request()->getPathInfo()]) }}" class="rounded-xl border border-blue-300/30 px-3 py-2 font-semibold text-blue-100 hover:bg-blue-400/10">เข้าสู่ระบบ</a>
                                    <a href="{{ route('register', ['redirect' => request()->getPathInfo()]) }}" class="rounded-xl bg-blue-600 px-3 py-2 font-semibold text-white hover:bg-blue-500">สมัครสมาชิก</a>
                                </div>
                            </div>
                        </div>
                    @else
                        @unless (auth()->user()?->hasVerifiedEmail())
                            <div class="mt-5 rounded-2xl border border-amber-400/20 bg-amber-500/10 px-4 py-4 text-sm text-amber-100">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="max-w-xl leading-7">บัญชีนี้ยังไม่ได้ยืนยันอีเมล ระบบจะยังไม่ sync รายการโปรดข้ามอุปกรณ์</p>
                                    <div class="flex shrink-0 gap-2">
                                        <a href="{{ route('verification.notice', ['redirect' => request()->getPathInfo()]) }}" class="rounded-xl border border-amber-300/30 px-3 py-2 font-semibold text-amber-100 hover:bg-amber-400/10">ไปยืนยันอีเมล</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="rounded-xl border border-white/10 px-3 py-2 font-semibold text-amber-100 transition hover:bg-white/[0.06] hover:text-white">ออกจากระบบ</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-5 flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-4 text-sm text-slate-300 sm:flex-row sm:items-center sm:justify-between">
                                <p class="leading-6">กำลัง sync รายการโปรดกับบัญชี {{ auth()->user()?->email }}</p>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl border border-white/10 px-3 py-2 font-semibold text-slate-300 transition hover:bg-white/[0.06] hover:text-white">ออกจากระบบ</button>
                                </form>
                            </div>
                        @endunless
                    @endguest
                </div>

                <div data-section-surface class="grid grid-cols-3 divide-x divide-white/10 overflow-hidden rounded-2xl border border-white/10 bg-white/[0.035]">
                    <div class="px-4 py-4 text-center">
                        <p data-favorites-total-number class="text-2xl font-semibold leading-none text-white">0</p>
                        <p data-section-muted class="mt-2 text-[11px] font-medium uppercase tracking-[0.08em] text-slate-500">{{ $copy['total_label'] }}</p>
                    </div>
                    <div class="px-4 py-4 text-center">
                        <p data-favorites-total-number="temple" class="text-2xl font-semibold leading-none text-white">0</p>
                        <p data-section-muted class="mt-2 text-[11px] font-medium uppercase tracking-[0.08em] text-slate-500">{{ $copy['temple_card_label'] }}</p>
                    </div>
                    <div class="px-4 py-4 text-center">
                        <p data-favorites-total-number="article" class="text-2xl font-semibold leading-none text-white">0</p>
                        <p data-section-muted class="mt-2 text-[11px] font-medium uppercase tracking-[0.08em] text-slate-500">{{ $copy['article_card_label'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div data-section-card data-section-card-padding data-favorites-empty class="mt-8 hidden rounded-3xl border border-dashed border-white/15 bg-white/[0.025] p-8 text-center shadow-lg shadow-slate-950/10 sm:p-10">
            <div data-section-surface data-section-accent class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-white/10 bg-white/[0.04] text-2xl text-amber-200">♡</div>
            <p class="mt-5 text-xl font-semibold leading-8 text-white">{{ $copy['empty_title'] }}</p>
            <p data-section-card-copy class="mx-auto mt-2 max-w-lg text-sm leading-7 text-slate-400">{{ $copy['empty_subtitle'] }}</p>
        </div>

        <div data-favorites-section="temple" class="mt-8 hidden">
            <div class="mb-5 flex items-end justify-between gap-3">
                <div>
                    <p data-section-accent class="text-xs font-semibold uppercase tracking-[0.14em] text-amber-300">{{ $copy['temple_eyebrow'] }}</p>
                    <h2 data-section-heading class="mt-1 text-2xl font-semibold leading-8 text-white">{{ $copy['temple_title'] }}</h2>
                </div>
                <span data-section-surface data-section-muted data-favorites-count="temple" class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-slate-400"></span>
            </div>
            <div data-section-items data-favorites-list="temple" class="grid items-stretch gap-5 md:grid-cols-2 xl:grid-cols-3"></div>
        </div>

        <div data-favorites-section="article" class="mt-10 hidden">
            <div class="mb-5 flex items-end justify-between gap-3">
                <div>
                    <p data-section-accent class="text-xs font-semibold uppercase tracking-[0.14em] text-blue-300">{{ $copy['article_eyebrow'] }}</p>
                    <h2 data-section-heading class="mt-1 text-2xl font-semibold leading-8 text-white">{{ $copy['article_title'] }}</h2>
                </div>
                <span data-section-surface data-section-muted data-favorites-count="article" class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-slate-400"></span>
            </div>
            <div data-section-items data-favorites-list="article" class="grid items-stretch gap-5 md:grid-cols-2 xl:grid-cols-3"></div>
        </div>
    </div>
</section>

@once
    <script>
        (() => {
            const storageKey = 'papaiwat_favorites';
            const itemsEndpoint = @json(route('favorites.items'));
            const favoriteEndpoint = @json(route('interactions.favorite'));
            const syncEndpoint = @json($canSyncUserFavorites ? route('favorites.sync') : null);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const readFavorites = () => {
                try {
                    const value = JSON.parse(localStorage.getItem(storageKey) || '[]');
                    if (!Array.isArray(value)) {
                        return [];
                    }

                    return value
                        .filter((item) => item?.type && item?.id)
                        .map((item) => ({
                            type: item.type,
                            id: Number(item.id),
                            addedAt: item.addedAt || null,
                        }));
                } catch (error) {
                    return [];
                }
            };

            const writeFavorites = (items) => {
                localStorage.setItem(storageKey, JSON.stringify(items
                    .filter((item) => item?.type && item?.id)
                    .map((item) => ({
                        type: item.type,
                        id: Number(item.id),
                        addedAt: item.addedAt || null,
                    }))
                ));
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

            const syncUserFavorites = async (items, mode = 'merge') => {
                if (!syncEndpoint) {
                    return null;
                }

                try {
                    const response = await fetch(syncEndpoint, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            mode,
                            items: mode === 'pull' ? [] : items.map((item) => ({
                                type: item.type,
                                id: item.id,
                                addedAt: item.addedAt || null,
                            })),
                        }),
                    });

                    if (!response.ok) {
                        return null;
                    }

                    const data = await response.json();
                    writeFavorites(data.items || []);

                    return data.items || [];
                } catch (error) {
                    return null;
                }
            };

            const hydrateFavorites = async (items) => {
                const syncedItems = await syncUserFavorites(items, 'pull');

                if (syncedItems) {
                    return syncedItems;
                }

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

                    const hydratedItems = items
                        .map((item) => {
                            const serverItem = serverItems.get(itemKey(item));

                            return serverItem
                                ? {
                                    ...serverItem,
                                    addedAt: item.addedAt || null,
                                }
                                : null;
                        })
                        .filter(Boolean);

                    writeFavorites(hydratedItems);

                    return hydratedItems;
                } catch (error) {
                    return [];
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
                total_label: 'ทั้งหมด',
                empty_image_text: 'No Image',
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

            const readAccountFavorites = (root) => {
                try {
                    const script = root.querySelector('[data-account-favorites]');
                    const value = script ? JSON.parse(script.textContent || '[]') : [];

                    return Array.isArray(value) ? value : [];
                } catch (error) {
                    return [];
                }
            };

            const mergeFavorites = (...groups) => {
                const merged = new Map();

                groups.flat().forEach((item) => {
                    if (item?.type && item?.id) {
                        merged.set(itemKey(item), {
                            type: item.type,
                            id: Number(item.id),
                            addedAt: item.addedAt || null,
                        });
                    }
                });

                return Array.from(merged.values());
            };

            const cardHtml = (item, copy) => {
                const key = itemKey(item);
                const typeLabel = item.type === 'temple' ? copy.temple_card_label : copy.article_card_label;
                const fallbackInitial = escapeHtml(typeLabel).slice(0, 1) || '?';
                const isTemple = item.type === 'temple';
                const addedAt = item.addedAt ? new Date(item.addedAt) : null;
                const addedLabel = addedAt && !Number.isNaN(addedAt.getTime())
                    ? addedAt.toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' })
                    : '';

                return `
                    <article data-section-card class="group flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/15 transition hover:border-white/20 hover:bg-white/[0.055]">
                        <div data-section-image data-section-surface class="relative aspect-[4/3] w-full overflow-hidden bg-slate-900">
                            ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">` : `<div class="flex h-full w-full items-center justify-center text-sm"><span data-section-surface class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.06] text-xl font-semibold text-white">${fallbackInitial}</span><span class="sr-only">${escapeHtml(copy.empty_image_text)}</span></div>`}
                            <div data-section-surface class="absolute left-3 top-3 rounded-full border border-white/15 bg-slate-950/70 px-2.5 py-1 text-[11px] font-medium text-white backdrop-blur">${escapeHtml(typeLabel)}</div>
                        </div>
                        <div data-section-card-padding class="flex min-w-0 flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="line-clamp-2 min-h-[3.5rem] text-lg font-semibold leading-7 text-white">${escapeHtml(item.title)}</h3>
                                <button type="button" data-remove-favorite="${escapeHtml(key)}" class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium text-slate-500 transition hover:bg-white/[0.06] hover:text-red-200">${escapeHtml(copy.remove_label)}</button>
                            </div>
                            <p data-section-card-copy class="mt-2 line-clamp-3 min-h-[4.5rem] text-sm leading-6 text-slate-400">${escapeHtml(item.excerpt || '')}</p>
                            <div class="mt-4 flex items-center justify-between gap-3 text-xs text-slate-500">
                                <span>${Number(item.count || 0).toLocaleString()} ${escapeHtml(copy.favorite_count_suffix)}</span>
                                ${addedLabel ? `<span>${escapeHtml(addedLabel)}</span>` : ''}
                            </div>
                            <div class="mt-auto pt-4">
                                <a href="${escapeHtml(item.url)}" data-section-button class="inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-white/[0.08] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/[0.12]">${escapeHtml(copy.open_label)}</a>
                            </div>
                        </div>
                    </article>
                `;
            };

            const renderRoot = async (root) => {
                const copy = readCopy(root);
                const accountItems = readAccountFavorites(root);

                if (accountItems.length > 0 && root.dataset.accountFavoritesApplied !== '1') {
                    writeFavorites(accountItems);
                    root.dataset.accountFavoritesApplied = '1';
                }

                const items = await hydrateFavorites(readFavorites());
                const groups = {
                    temple: items.filter((item) => item.type === 'temple'),
                    article: items.filter((item) => item.type === 'article'),
                };

                root.querySelector('[data-favorites-empty]')?.classList.toggle('hidden', items.length > 0);
                const total = root.querySelector('[data-favorites-total]');
                if (total) {
                    total.textContent = `${copy.total_label} ${items.length.toLocaleString()} ${copy.section_count_suffix}`;
                }
                root.querySelector('[data-favorites-total-number]:not([data-favorites-total-number="temple"]):not([data-favorites-total-number="article"])')?.replaceChildren(document.createTextNode(items.length.toLocaleString()));
                root.querySelector('[data-favorites-total-number="temple"]')?.replaceChildren(document.createTextNode(groups.temple.length.toLocaleString()));
                root.querySelector('[data-favorites-total-number="article"]')?.replaceChildren(document.createTextNode(groups.article.length.toLocaleString()));

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
                    button.addEventListener('click', async () => {
                        const key = button.dataset.removeFavorite;
                        const item = readFavorites().find((favorite) => itemKey(favorite) === key);
                        const nextItems = readFavorites().filter((favorite) => itemKey(favorite) !== key);

                        button.disabled = true;
                        button.classList.add('opacity-50');
                        writeFavorites(nextItems);

                        if (item) {
                            syncFavoriteCount(item, 'remove').catch(() => {});
                        }

                        await syncUserFavorites(nextItems, 'replace');
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
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    renderAll();
                }
            });
            window.addEventListener('focus', renderAll);

            renderAll();
        })();
    </script>
@endonce
