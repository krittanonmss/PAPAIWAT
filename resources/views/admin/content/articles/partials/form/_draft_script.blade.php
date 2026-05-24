<script>
    (() => {
        const storageKey = `papaiwat:article-form-draft:${window.location.pathname}`;
        const hasServerErrors = @json($errors->any());
        const draftStorage = window.sessionStorage;

        const getForm = () => document.getElementById('article-form');

        const readArticleDraft = () => {
            try {
                return JSON.parse(draftStorage.getItem(storageKey)) || {};
            } catch {
                return {};
            }
        };

        window.articleDraft = function (name, fallback = '') {
            if (hasServerErrors) {
                return fallback;
            }

            const fields = readArticleDraft().fields || {};

            return Object.prototype.hasOwnProperty.call(fields, name)
                ? fields[name]
                : fallback;
        };

        window.normalizeArticleMediaIds = function (value) {
            const values = Array.isArray(value) ? value : [value];

            return values
                .map((item) => String(item ?? '').trim())
                .filter((item) => /^\d+$/.test(item));
        };

        window.articleDraftMediaId = function (name, fallback = '') {
            const fallbackIds = window.normalizeArticleMediaIds(fallback);
            const draftIds = window.normalizeArticleMediaIds(window.articleDraft(name, fallback));

            return draftIds[0] ?? fallbackIds[0] ?? '';
        };

        window.quickArticleMediaUploader = function () {
            return {
                isUploading: false,
                errorMessage: '',

                async upload() {
                    this.errorMessage = '';

                    const files = Array.from(this.$refs.fileInput.files || []);

                    if (files.length === 0) {
                        this.errorMessage = 'กรุณาเลือกรูปก่อนอัปโหลด';
                        return;
                    }

                    const maxFileSize = 5 * 1024 * 1024;
                    const invalidFile = files.find((file) => !file.type.startsWith('image/'));
                    const oversizedFile = files.find((file) => file.size > maxFileSize);

                    if (invalidFile) {
                        this.errorMessage = 'อัปโหลดได้เฉพาะไฟล์รูปภาพเท่านั้น';
                        return;
                    }

                    if (oversizedFile) {
                        this.errorMessage = `ไฟล์ ${oversizedFile.name} มีขนาดเกิน 5 MB`;
                        return;
                    }

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('visibility', 'public');
                    files.forEach((file) => formData.append('files[]', file));

                    this.isUploading = true;

                    try {
                        const response = await fetch('{{ route('admin.media.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (!response.ok) {
                            const payload = await response.json().catch(() => null);
                            this.errorMessage = payload?.message || 'อัปโหลดไม่สำเร็จ กรุณาตรวจสอบไฟล์อีกครั้ง';
                            return;
                        }

                        window.location.reload();
                    } catch (error) {
                        this.errorMessage = 'เกิดข้อผิดพลาดระหว่างอัปโหลด';
                    } finally {
                        this.isUploading = false;
                    }
                },
            };
        };

        const registerArticleRichTextFormats = () => {
            if (window.articleRichTextFormatsRegistered || !window.Quill) {
                return;
            }

            const Parchment = window.Quill.import('parchment');
            const LineHeight = new Parchment.ClassAttributor('lineheight', 'ql-lineheight', {
                scope: Parchment.Scope.BLOCK,
                whitelist: ['tight', 'normal', 'relaxed', 'loose'],
            });

            window.Quill.register(LineHeight, true);
            window.articleRichTextFormatsRegistered = true;
        };

        const initArticleRichEditors = () => {
            if (!window.Quill) {
                return;
            }

            registerArticleRichTextFormats();

            document.querySelectorAll('[data-rich-editor]').forEach((wrapper) => {
                if (wrapper.dataset.richEditorBound === 'true') {
                    return;
                }

                const input = wrapper.querySelector('[data-rich-editor-input]');
                const editorBody = wrapper.querySelector('[data-editor-body]');
                const sourceEditor = wrapper.querySelector('[data-editor-source]');
                const sourceToggle = wrapper.querySelector('[data-editor-source-toggle]');
                const toolbar = wrapper.querySelector('[data-editor-toolbar]');
                const counter = wrapper.querySelector('[data-editor-count]');
                const modeLabel = wrapper.querySelector('[data-editor-mode-label]');

                if (!input || !editorBody || !toolbar) {
                    return;
                }

                wrapper.dataset.richEditorBound = 'true';

                const quill = new Quill(editorBody, {
                    theme: 'snow',
                    placeholder: wrapper.dataset.placeholder || '',
                    modules: {
                        history: {
                            delay: 1000,
                            maxStack: 100,
                            userOnly: true,
                        },
                        toolbar,
                    },
                    formats: [
                        'blockquote',
                        'bold',
                        'code-block',
                        'header',
                        'indent',
                        'italic',
                        'lineheight',
                        'link',
                        'list',
                        'script',
                        'strike',
                        'underline',
                    ],
                });

                quill.root.innerHTML = input.value || '';
                input._quill = quill;
                if (sourceEditor) {
                    sourceEditor.value = input.value || '';
                }

                const dispatchChange = () => {
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                };

                const updateCounter = () => {
                    if (counter) {
                        counter.textContent = `${Math.max(quill.getLength() - 1, 0).toLocaleString()} ตัวอักษร`;
                    }
                };

                const sync = () => {
                    const html = quill.root.innerHTML.trim();
                    input.value = html === '<p><br></p>' ? '' : html;

                    if (sourceEditor && sourceEditor.classList.contains('hidden')) {
                        sourceEditor.value = input.value;
                    }

                    updateCounter();
                    dispatchChange();
                };

                quill.on('text-change', sync);
                updateCounter();

                if (sourceEditor) {
                    sourceEditor.addEventListener('input', () => {
                        input.value = sourceEditor.value.trim();
                        dispatchChange();
                    });
                }

                if (sourceToggle && sourceEditor) {
                    sourceToggle.addEventListener('click', () => {
                        const sourceIsHidden = sourceEditor.classList.contains('hidden');

                        if (sourceIsHidden) {
                            sourceEditor.value = input.value;
                            editorBody.classList.add('hidden');
                            sourceEditor.classList.remove('hidden');
                            sourceToggle.classList.add('text-blue-300');
                            if (modeLabel) {
                                modeLabel.textContent = 'HTML source';
                            }
                            return;
                        }

                        input.value = sourceEditor.value.trim();
                        quill.root.innerHTML = input.value || '';
                        sourceEditor.classList.add('hidden');
                        editorBody.classList.remove('hidden');
                        sourceToggle.classList.remove('text-blue-300');
                        if (modeLabel) {
                            modeLabel.textContent = 'Rich text';
                        }
                        updateCounter();
                        dispatchChange();
                    });
                }
            });
        };

        const saveArticleDraft = () => {
            const form = getForm();

            if (!form) {
                return;
            }

            const draft = {
                fields: {},
                checks: {},
            };

            form.querySelectorAll('input, textarea, select').forEach((field) => {
                if (!field.name || field.disabled || field.name === '_token' || field.name === '_method') {
                    return;
                }

                if (field.type === 'checkbox') {
                    draft.checks[field.name] ??= [];

                    if (field.checked) {
                        draft.checks[field.name].push(field.value);
                    }

                    return;
                }

                draft.fields[field.name] = field.value;
            });

            draftStorage.setItem(storageKey, JSON.stringify(draft));
        };

        window.saveArticleDraft = saveArticleDraft;

        const restoreArticleDraft = () => {
            const form = getForm();

            if (!form) {
                return;
            }

            if (hasServerErrors) {
                return;
            }

            const rawDraft = draftStorage.getItem(storageKey);

            if (!rawDraft) {
                return;
            }

            let draft = {};

            try {
                draft = JSON.parse(rawDraft);
            } catch {
                return;
            }

            form.querySelectorAll('input, textarea, select').forEach((field) => {
                if (!field.name || field.disabled || field.name === '_token' || field.name === '_method') {
                    return;
                }

                if (field.type === 'checkbox') {
                    const values = draft.checks?.[field.name];

                    if (Array.isArray(values)) {
                        field.checked = values.includes(field.value);
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    return;
                }

                if (draft.fields?.[field.name] !== undefined) {
                    field.value = draft.fields[field.name];

                    if (field.matches('[data-rich-editor-input]') && field._quill) {
                        field._quill.root.innerHTML = field.value || '';
                        const sourceEditor = field
                            .closest('[data-rich-editor]')
                            ?.querySelector('[data-editor-source]');

                        if (sourceEditor) {
                            sourceEditor.value = field.value || '';
                        }
                    }

                    field.dispatchEvent(new Event('input', { bubbles: true }));
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

        };

        window.addEventListener('load', () => {
            initArticleRichEditors();
            restoreArticleDraft();

            const form = getForm();

            if (!form) {
                return;
            }

            form.addEventListener('input', saveArticleDraft);
            form.addEventListener('change', saveArticleDraft);

            form.addEventListener('submit', () => {
                initArticleRichEditors();
                draftStorage.removeItem(storageKey);
            });
        });
    })();
</script>
