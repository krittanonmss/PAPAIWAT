<div
    class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
    data-cms-page-preview
    data-preview-url="{{ $previewUrl }}"
>
    <div class="flex flex-col gap-3 border-b border-white/10 p-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-300">Realtime Preview</p>
            <h2 class="mt-1 text-lg font-semibold text-white">ตัวอย่างหน้าเว็บ</h2>
        </div>

        <div
            class="hidden rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-200"
            data-cms-page-preview-loading
        >
            กำลังอัปเดต...
        </div>
    </div>

    <div class="relative h-[620px] bg-slate-950/60">
        <iframe
            title="CMS page realtime preview"
            class="h-full w-full bg-slate-950"
            sandbox="allow-scripts allow-same-origin"
            data-cms-page-preview-frame
        ></iframe>
    </div>

    <div
        class="hidden border-t border-rose-400/20 bg-rose-500/10 px-5 py-3 text-sm text-rose-200"
        data-cms-page-preview-error
    ></div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-cms-page-preview]').forEach((preview) => {
                const form = preview.closest('form');
                const frame = preview.querySelector('[data-cms-page-preview-frame]');
                const loading = preview.querySelector('[data-cms-page-preview-loading]');
                const error = preview.querySelector('[data-cms-page-preview-error]');
                const previewUrl = preview.dataset.previewUrl;
                let timer = null;
                let controller = null;

                if (!form || !frame || !previewUrl) {
                    return;
                }

                const setLoading = (value) => {
                    loading?.classList.toggle('hidden', !value);
                };

                const setError = (message = '') => {
                    if (!error) {
                        return;
                    }

                    error.textContent = message;
                    error.classList.toggle('hidden', !message);
                };

                const renderPreview = async () => {
                    controller?.abort();
                    controller = new AbortController();
                    const formData = new FormData(form);

                    formData.delete('_method');

                    setLoading(true);
                    setError('');

                    try {
                        const response = await fetch(previewUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                Accept: 'application/json',
                            },
                            signal: controller.signal,
                        });

                        if (!response.ok) {
                            throw new Error(`Preview request failed (${response.status})`);
                        }

                        const payload = await response.json();
                        frame.srcdoc = payload.html || '';
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            setError(error.message || 'ไม่สามารถโหลด preview ได้');
                        }
                    } finally {
                        setLoading(false);
                    }
                };

                const schedulePreview = () => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(renderPreview, 350);
                };

                form.addEventListener('input', schedulePreview);
                form.addEventListener('change', schedulePreview);
                renderPreview();
            });
        });
    </script>
@endonce
