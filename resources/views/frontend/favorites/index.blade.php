@extends('frontend.layouts.app')

@section('title', 'รายการโปรด')
@section('meta_description', 'รายการโปรดที่บันทึกไว้ในเบราว์เซอร์ของคุณ')

@section('content')
<main class="min-h-screen bg-slate-950 px-4 py-10 text-white">
    <section class="mx-auto max-w-6xl">
        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30">
            <p class="text-sm font-medium text-amber-300">Local favorites</p>
            <h1 class="mt-2 text-3xl font-bold">รายการโปรดของฉัน</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-400">
                รายการนี้เก็บไว้ในเครื่อง/เบราว์เซอร์นี้เท่านั้น ระบบนับเฉพาะจำนวนรวมของรายการที่ถูกบันทึก
                โดยไม่เก็บว่าใครเป็นคนบันทึก ถ้าล้าง cache, ล้าง browser data หรือเปลี่ยนอุปกรณ์ รายการโปรดจะหาย
            </p>
        </div>

        <div id="favorites-empty" class="mt-6 hidden rounded-3xl border border-white/10 bg-white/[0.04] p-10 text-center">
            <p class="text-lg font-semibold text-white">ยังไม่มีรายการโปรด</p>
            <p class="mt-2 text-sm text-slate-500">กดปุ่มหัวใจในหน้าวัดหรือบทความเพื่อเพิ่มรายการโปรด</p>
        </div>

        <div id="favorites-list" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3"></div>
    </section>
</main>

<script>
    (() => {
        const storageKey = 'papaiwat_favorites';
        const itemsEndpoint = @json(route('favorites.items'));
        const favoriteEndpoint = @json(route('interactions.favorite'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const list = document.getElementById('favorites-list');
        const empty = document.getElementById('favorites-empty');

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

        const render = async () => {
            const items = await hydrateFavorites(readFavorites());
            list.innerHTML = '';
            empty.classList.toggle('hidden', items.length > 0);

            items.forEach((item) => {
                const key = itemKey(item);
                const card = document.createElement('article');
                card.className = 'overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-lg shadow-slate-950/20';
                card.innerHTML = `
                    ${item.image ? `<img src="${escapeHtml(item.image)}" alt="" class="aspect-[16/9] w-full object-cover">` : `<div class="aspect-[16/9] w-full bg-slate-900"></div>`}
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <span class="rounded-full border border-white/10 px-2.5 py-1 text-xs text-slate-400">${item.type === 'temple' ? 'วัด' : 'บทความ'}</span>
                            <span class="text-xs text-red-200">${Number(item.count || 0).toLocaleString()} รายการโปรด</span>
                        </div>
                        <h2 class="mt-3 line-clamp-2 text-lg font-semibold text-white">${escapeHtml(item.title)}</h2>
                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-400">${escapeHtml(item.excerpt || '')}</p>
                        <div class="mt-5 flex gap-2">
                            <a href="${escapeHtml(item.url)}" class="inline-flex flex-1 items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">เปิดดู</a>
                            <button type="button" data-remove-favorite="${escapeHtml(key)}" class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/10">ลบ</button>
                        </div>
                    </div>
                `;
                list.appendChild(card);
            });

            list.querySelectorAll('[data-remove-favorite]').forEach((button) => {
                button.addEventListener('click', () => {
                    const key = button.dataset.removeFavorite;
                    const item = readFavorites().find((favorite) => itemKey(favorite) === key);
                    writeFavorites(readFavorites().filter((favorite) => itemKey(favorite) !== key));
                    if (item) {
                        syncFavoriteCount(item, 'remove').catch(() => {});
                    }
                    render();
                });
            });
        };

        window.addEventListener('storage', (event) => {
            if (event.key === storageKey) {
                render();
            }
        });
        window.addEventListener('papaiwat:favorites-updated', render);

        render();
    })();
</script>
@endsection
