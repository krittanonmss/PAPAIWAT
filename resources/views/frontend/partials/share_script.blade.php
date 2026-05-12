<script>
    (() => {
        if (window.PapaiwatShareReady) {
            return;
        }

        window.PapaiwatShareReady = true;

        const endpoint = @json(route('interactions.share'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        let activeShare = null;

        const updateCount = (type, id, count) => {
            document.querySelectorAll(`[data-share-count="${type}:${id}"]`).forEach((element) => {
                element.textContent = Number(count || 0).toLocaleString();
            });
        };

        const syncShareCount = async (type, id) => {
            if (!type || !id) {
                return;
            }

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ type, id }),
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            updateCount(type, id, data.count);
        };

        const copyText = async (text) => {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return;
            }

            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            textarea.remove();
        };

        const setLabel = (button, text) => {
            button.querySelectorAll('[data-share-label]').forEach((element) => {
                element.textContent = text;
            });
        };

        const flashLabel = (button, text) => {
            const defaultLabel = button.dataset.shareDefaultLabel || 'แชร์';
            setLabel(button, text);
            window.clearTimeout(button._shareLabelTimer);
            button._shareLabelTimer = window.setTimeout(() => setLabel(button, defaultLabel), 1800);
        };

        const ensureDialog = () => {
            if (document.getElementById('share-choice-dialog')) {
                return;
            }

            const dialog = document.createElement('div');
            dialog.id = 'share-choice-dialog';
            dialog.className = 'fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/45 px-4 py-6 backdrop-blur-[2px]';
            dialog.innerHTML = `
                <div class="w-full max-w-3xl scale-95 rounded-3xl border border-white/10 bg-slate-950 p-5 text-white opacity-0 shadow-2xl shadow-slate-950/60 transition duration-200" data-share-sheet role="dialog" aria-modal="true" aria-labelledby="share-choice-title">
                    <div class="flex items-center justify-between gap-4">
                        <h2 id="share-choice-title" class="text-xl font-semibold">แชร์</h2>
                        <button type="button" data-share-close class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-xl leading-none transition hover:bg-white/15" aria-label="ปิด">&times;</button>
                    </div>
                    <div class="relative mt-6">
                        <button type="button" data-share-scroll="left" class="absolute left-0 top-1/2 z-10 hidden h-10 w-10 -translate-x-3 -translate-y-1/2 items-center justify-center rounded-full border border-white/10 bg-slate-900/95 text-white shadow-lg transition hover:bg-slate-800 sm:inline-flex" aria-label="เลื่อนซ้าย">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                            </svg>
                        </button>
                        <div data-share-scroll-list class="flex gap-5 overflow-x-auto scroll-smooth px-1 pb-3 sm:px-10" style="scrollbar-width: none;">
                            <button type="button" data-share-target="copy" class="w-20 shrink-0 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-slate-950 shadow-lg shadow-slate-950/20 transition hover:scale-105">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 8.75A2.75 2.75 0 0 1 10.75 6h6.5A2.75 2.75 0 0 1 20 8.75v6.5A2.75 2.75 0 0 1 17.25 18h-6.5A2.75 2.75 0 0 1 8 15.25v-6.5Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 16.5a2 2 0 0 1-2-2v-7A3.5 3.5 0 0 1 7.5 4h7a2 2 0 0 1 2 2" />
                                    </svg>
                                </span>
                                <span class="mt-2 block text-xs font-medium leading-tight text-slate-200" data-share-copy-status>Copy link / ฝัง</span>
                            </button>
                            <button type="button" data-share-target="facebook" class="w-20 shrink-0 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#1877f2] text-white shadow-lg shadow-slate-950/20 transition hover:scale-105">
                                    <span class="text-2xl font-bold leading-none">f</span>
                                </span>
                                <span class="mt-2 block text-xs font-medium text-slate-200">Facebook</span>
                            </button>
                            <button type="button" data-share-target="x" class="w-20 shrink-0 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-slate-950 shadow-lg shadow-slate-950/20 transition hover:scale-105">
                                    <span class="text-xl font-bold leading-none">X</span>
                                </span>
                                <span class="mt-2 block text-xs font-medium text-slate-200">X</span>
                            </button>
                            <button type="button" data-share-target="messages" class="w-20 shrink-0 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#34c759] text-white shadow-lg shadow-slate-950/20 transition hover:scale-105">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 18.5 4 20l1.2-3.6A7.5 7.5 0 1 1 7.5 18.5Z" />
                                    </svg>
                                </span>
                                <span class="mt-2 block text-xs font-medium text-slate-200">Messages</span>
                            </button>
                            <button type="button" data-share-target="whatsapp" class="w-20 shrink-0 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#25d366] text-white shadow-lg shadow-slate-950/20 transition hover:scale-105">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.3 18.2 4 20l.7-2.8A8 8 0 1 1 6.3 18.2Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.2 8.8c.2-.5.4-.5.7-.5h.5c.2 0 .4.1.5.4l.7 1.6c.1.3 0 .5-.2.7l-.4.5c.6 1.1 1.4 1.9 2.5 2.5l.5-.4c.2-.2.5-.3.7-.2l1.6.7c.3.1.4.3.4.6v.4c0 .4 0 .6-.5.8-.5.3-1.3.4-2.1.2-2.6-.7-5-3.1-5.7-5.7-.2-.8-.1-1.6.3-2.1Z" />
                                    </svg>
                                </span>
                                <span class="mt-2 block text-xs font-medium text-slate-200">WhatsApp</span>
                            </button>
                            <button type="button" data-share-target="more" class="w-20 shrink-0 text-center">
                                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-800 text-white shadow-lg shadow-slate-950/20 transition hover:scale-105">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm6 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm6 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                    </svg>
                                </span>
                                <span class="mt-2 block text-xs font-medium text-slate-200">More</span>
                            </button>
                        </div>
                        <button type="button" data-share-scroll="right" class="absolute right-0 top-1/2 z-10 hidden h-10 w-10 translate-x-3 -translate-y-1/2 items-center justify-center rounded-full border border-white/10 bg-slate-900/95 text-white shadow-lg transition hover:bg-slate-800 sm:inline-flex" aria-label="เลื่อนขวา">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(dialog);

            dialog.addEventListener('click', (event) => {
                if (event.target === dialog || event.target.closest('[data-share-close]')) {
                    closeDialog();
                }
            });

            dialog.querySelectorAll('[data-share-target]').forEach((button) => {
                button.addEventListener('click', () => handleShareTarget(button.dataset.shareTarget));
            });

            dialog.querySelectorAll('[data-share-scroll]').forEach((button) => {
                button.addEventListener('click', () => {
                    const list = dialog.querySelector('[data-share-scroll-list]');
                    const direction = button.dataset.shareScroll === 'left' ? -1 : 1;

                    list?.scrollBy({ left: direction * 180, behavior: 'smooth' });
                });
            });
        };

        const closeDialog = () => {
            const dialog = document.getElementById('share-choice-dialog');

            if (!dialog) {
                return;
            }

            dialog.classList.add('hidden');
            dialog.classList.remove('flex');
            const sheet = dialog.querySelector('[data-share-sheet]');
            sheet?.classList.add('scale-95', 'opacity-0');
            activeShare = null;
        };

        const openDialog = (shareData) => {
            ensureDialog();
            activeShare = shareData;

            const dialog = document.getElementById('share-choice-dialog');
            const copyStatus = dialog.querySelector('[data-share-copy-status]');
            if (copyStatus) {
                copyStatus.textContent = 'Copy link / ฝัง';
            }

            dialog.classList.remove('hidden');
            dialog.classList.add('flex');
            window.requestAnimationFrame(() => {
                const sheet = dialog.querySelector('[data-share-sheet]');
                sheet?.classList.remove('scale-95', 'opacity-0');
            });
        };

        const countShare = () => {
            if (!activeShare) {
                return;
            }

            syncShareCount(activeShare.type, activeShare.id).catch(() => {});
            flashLabel(activeShare.button, 'แชร์แล้ว');
        };

        const openShareWindow = (url) => {
            window.open(url, '_blank', 'noopener,noreferrer,width=720,height=640');
        };

        const handleShareTarget = async (target) => {
            if (!activeShare) {
                return;
            }

            const encodedUrl = encodeURIComponent(activeShare.url);
            const encodedText = encodeURIComponent(activeShare.text || activeShare.title || '');

            try {
                if (target === 'line') {
                    openShareWindow(`https://social-plugins.line.me/lineit/share?url=${encodedUrl}`);
                    countShare();
                    closeDialog();
                    return;
                }

                if (target === 'facebook') {
                    openShareWindow(`https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`);
                    countShare();
                    closeDialog();
                    return;
                }

                if (target === 'x') {
                    openShareWindow(`https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedText}`);
                    countShare();
                    closeDialog();
                    return;
                }

                if (target === 'messages') {
                    window.location.href = `sms:?&body=${encodedText}%20${encodedUrl}`;
                    countShare();
                    closeDialog();
                    return;
                }

                if (target === 'whatsapp') {
                    openShareWindow(`https://wa.me/?text=${encodedText}%20${encodedUrl}`);
                    countShare();
                    closeDialog();
                    return;
                }

                if (target === 'more' && navigator.share) {
                    await navigator.share({
                        title: activeShare.title,
                        text: activeShare.text,
                        url: activeShare.url,
                    });
                    countShare();
                    closeDialog();
                    return;
                }

                await copyText(activeShare.url);
                document.querySelector('[data-share-copy-status]').textContent = 'คัดลอกแล้ว';
                countShare();
                window.setTimeout(closeDialog, 700);
            } catch (error) {
                flashLabel(activeShare.button, 'แชร์ไม่สำเร็จ');
            }
        };

        const init = () => {
            ensureDialog();

            document.querySelectorAll('[data-share-button]').forEach((button) => {
                if (button.dataset.shareReady === '1') {
                    return;
                }

                button.dataset.shareReady = '1';

                button.addEventListener('click', async () => {
                    const title = button.dataset.shareTitle || document.title;
                    const text = button.dataset.shareText || title;
                    const url = button.dataset.shareUrl || window.location.href;
                    const type = button.dataset.shareType;
                    const id = button.dataset.shareId;

                    openDialog({ button, title, text, url, type, id });
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
