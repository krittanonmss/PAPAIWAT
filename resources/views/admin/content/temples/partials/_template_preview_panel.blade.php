@php
    $content = $temple?->content;
    $selectedTemplateId = old('template_id', $content?->template_id);
    $templatePreviewUrl = $templatePreviewUrl ?? null;
    $templatePreviewSrc = $templatePreviewUrl
        ? $templatePreviewUrl . '?' . http_build_query(array_filter([
            'template_id' => $selectedTemplateId,
            '_preview_ts' => time(),
        ]))
        : null;
@endphp

@if ($templatePreviewSrc)
    <section
        class="overflow-hidden rounded-3xl border border-white/10 bg-slate-950/60 shadow-xl shadow-slate-950/30 backdrop-blur"
        data-template-preview-live-url="{{ $templatePreviewLiveUrl ?? '' }}"
    >
        <div class="border-b border-white/10 px-5 py-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">Temple Preview</p>
                    <h2 class="mt-1 text-lg font-semibold text-white">ตัวอย่างหน้าแสดงผลวัด</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-400">อัปเดตจากข้อมูลในฟอร์มแบบ realtime ก่อนบันทึกจริง</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        data-template-preview-refresh
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2 text-xs font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        รีเฟรช preview
                    </button>

                    <a
                        href="{{ $templatePreviewSrc }}"
                        target="_blank"
                        rel="noopener"
                        data-template-preview-open
                        class="inline-flex items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10 px-4 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
                    >
                        เปิดเต็มหน้า
                    </a>
                </div>
            </div>
        </div>

        <div class="relative h-[360px] bg-slate-950 md:h-[460px] xl:h-[560px]">
            <iframe
                id="temple-template-preview"
                src="{{ $templatePreviewSrc }}"
                title="ตัวอย่างหน้าแสดงผลวัด"
                class="h-full w-full bg-slate-950"
                loading="lazy"
            ></iframe>
        </div>
    </section>

    @once
        <script>
            (() => {
                const bindTempleTemplatePreview = () => {
                    document.querySelectorAll('[data-template-preview-select]').forEach((select) => {
                        if (select.dataset.previewBound === '1') {
                            return;
                        }

                        select.dataset.previewBound = '1';

                        const form = select.closest('form');
                        const frame = document.getElementById(select.dataset.previewTarget);
                        const previewBox = frame?.closest('[data-template-preview-live-url]');
                        const liveUrl = previewBox?.dataset.templatePreviewLiveUrl;
                        let previewTimer = null;
                        let previewController = null;

                        const updateStaticUrl = () => {
                            const baseUrl = select.dataset.previewBase;
                            const openLink = previewBox?.querySelector('[data-template-preview-open]');

                            if (!baseUrl || !frame) {
                                return;
                            }

                            const url = new URL(baseUrl, window.location.origin);

                            if (select.value) {
                                url.searchParams.set('template_id', select.value);
                            }

                            if (openLink) {
                                openLink.href = url.toString();
                            }
                        };

                        const renderLivePreview = async () => {
                            updateStaticUrl();

                            if (!form || !frame || !liveUrl) {
                                return;
                            }

                            previewController?.abort();
                            previewController = new AbortController();

                            const formData = new FormData(form);
                            formData.delete('_method');

                            try {
                                const response = await fetch(liveUrl, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        Accept: 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    signal: previewController.signal,
                                });

                                const payload = await response.json();
                                frame.srcdoc = payload.html || '';
                            } catch (error) {
                                if (error.name !== 'AbortError') {
                                    frame.srcdoc = '<div style="font-family: sans-serif; padding: 2rem; color: white; background: #020617;">Preview failed</div>';
                                }
                            }
                        };

                        const scheduleLivePreview = () => {
                            window.clearTimeout(previewTimer);
                            previewTimer = window.setTimeout(renderLivePreview, 350);
                        };

                        select.addEventListener('change', scheduleLivePreview);
                        form?.addEventListener('input', scheduleLivePreview, true);
                        form?.addEventListener('change', scheduleLivePreview, true);

                        previewBox
                            ?.querySelector('[data-template-preview-refresh]')
                            ?.addEventListener('click', renderLivePreview);

                        renderLivePreview();
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', bindTempleTemplatePreview);
                } else {
                    bindTempleTemplatePreview();
                }
            })();
        </script>
    @endonce
@endif
