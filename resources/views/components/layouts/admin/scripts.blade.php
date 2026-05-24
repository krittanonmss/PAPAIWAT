    <script>
        (() => {
            if (@js($adminOpenDetailInNewTab)) {
                document.querySelectorAll('.admin-content a[href]').forEach((link) => {
                    const label = (link.textContent || '').trim();

                    if (! label.match(/รายละเอียด|ดูรายละเอียด/)) {
                        return;
                    }

                    const url = new URL(link.href, window.location.origin);

                    if (url.origin !== window.location.origin || ! url.pathname.startsWith('/admin/')) {
                        return;
                    }

                    link.target = '_blank';
                    link.rel = 'noopener noreferrer';
                });
            }

            const storage = window.sessionStorage;
            const namespace = 'papaiwat:admin-form-draft:';
            const ignoredFormIds = new Set(['article-form', 'temple-form']);
            const skippedFieldNames = new Set(['_token', '_method', 'password', 'password_confirmation']);

            const shouldAutosave = (form) => {
                const method = (form.getAttribute('method') || 'GET').toUpperCase();

                if (form.dataset.adminAutosave === 'off' || method === 'GET' || ignoredFormIds.has(form.id)) {
                    return false;
                }

                if (form.closest('aside') && form.action.includes('/logout')) {
                    return false;
                }

                return Boolean(form.querySelector('input[name], textarea[name], select[name]'));
            };

            const storageKey = (form) => {
                const action = form.getAttribute('action') || window.location.pathname;
                const method = (form.getAttribute('method') || 'POST').toUpperCase();

                return namespace + method + ':' + action;
            };

            const readDraft = (key) => {
                try {
                    return JSON.parse(storage.getItem(key)) || {};
                } catch (error) {
                    return {};
                }
            };

            const collect = (form) => {
                const fields = {};
                const checks = {};

                form.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
                    if (field.disabled || skippedFieldNames.has(field.name) || field.type === 'file') {
                        return;
                    }

                    if (field.type === 'hidden' && field.name.startsWith('_')) {
                        return;
                    }

                    if (field.type === 'checkbox' || field.type === 'radio') {
                        checks[field.name] ??= [];

                        if (field.checked) {
                            checks[field.name].push(field.value);
                        }

                        return;
                    }

                    fields[field.name] = field.value;
                });

                return { fields, checks };
            };

            const save = (form) => {
                storage.setItem(storageKey(form), JSON.stringify(collect(form)));
            };

            const restore = (form) => {
                const draft = readDraft(storageKey(form));

                form.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
                    if (field.disabled || skippedFieldNames.has(field.name) || field.type === 'file') {
                        return;
                    }

                    if (field.type === 'hidden' && field.name.startsWith('_')) {
                        return;
                    }

                    if (field.type === 'checkbox' || field.type === 'radio') {
                        const values = draft.checks?.[field.name];

                        if (Array.isArray(values)) {
                            field.checked = values.includes(field.value);
                            field.dispatchEvent(new Event('change', { bubbles: true }));
                        }

                        return;
                    }

                    if (Object.prototype.hasOwnProperty.call(draft.fields || {}, field.name)) {
                        field.value = draft.fields[field.name] ?? '';
                        field.dispatchEvent(new Event('input', { bubbles: true }));
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            };

            const bind = () => {
                document.querySelectorAll('form').forEach((form) => {
                    if (!shouldAutosave(form) || form.dataset.adminAutosaveBound === 'true') {
                        return;
                    }

                    form.dataset.adminAutosaveBound = 'true';
                    restore(form);

                    form.addEventListener('input', () => save(form), true);
                    form.addEventListener('change', () => save(form), true);
                    form.addEventListener('submit', () => storage.removeItem(storageKey(form)));
                    window.addEventListener('beforeunload', () => save(form));
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bind);
            } else {
                bind();
            }
        })();
    </script>
