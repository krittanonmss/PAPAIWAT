<script>
    window.templeFormHasServerErrors = @json($errors->any());

    const templeDraftStore = {
        key: 'papaiwat:temple-form-draft:' + window.location.pathname,
        storage: window.sessionStorage,

        read() {
            try {
                return JSON.parse(this.storage.getItem(this.key)) || {};
            } catch (error) {
                return {};
            }
        },

        write(payload) {
            this.storage.setItem(this.key, JSON.stringify({
                ...this.read(),
                ...payload,
            }));
        },

        get(key, fallback = null) {
            const draft = this.read();

            return Object.prototype.hasOwnProperty.call(draft, key)
                ? draft[key]
                : fallback;
        },

        clear() {
            this.storage.removeItem(this.key);
        },
    };

    window.templeDraft = Object.assign(function (name, fallback = '') {
        if (window.templeFormHasServerErrors) {
            return fallback;
        }

        const fields = window.templeDraft.get('fields', {});

        return Object.prototype.hasOwnProperty.call(fields, name)
            ? fields[name]
            : fallback;
    }, templeDraftStore);

    window.normalizeTempleMediaIds = function (value) {
        const values = Array.isArray(value) ? value : [value];

        return values
            .map((item) => String(item ?? '').trim())
            .filter((item) => /^\d+$/.test(item));
    };

    window.templeDraftMediaId = function (name, fallback = '') {
        const fallbackIds = window.normalizeTempleMediaIds(fallback);
        const draftIds = window.normalizeTempleMediaIds(window.templeDraft(name, fallback));

        return draftIds[0] ?? fallbackIds[0] ?? '';
    };

    window.templeDraftMediaIdArray = function (name, fallback = []) {
        const fallbackIds = window.normalizeTempleMediaIds(fallback);
        const draftIds = window.normalizeTempleMediaIds(window.templeDraft(name, fallback));

        return draftIds.length > 0 ? draftIds : fallbackIds;
    };

    function getTempleForm() {
        return document.getElementById('temple-form');
    }

    function quickMediaUploader() {
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
    }

    function updatePrimaryCategoryOptions() {
        const form = getTempleForm();
        const primarySelect = document.getElementById('primary_category_id');

        if (!form || !primarySelect) {
            return;
        }

        const categoryInputs = Array.from(form.querySelectorAll('input[name="category_ids[]"]'));
        const categoryMarker = form.querySelector('[data-async-multi-field="category_ids"]');

        if (categoryInputs.length === 0 && !categoryMarker) {
            return;
        }

        const selecteds = categoryInputs
            .filter((input) => input.type !== 'checkbox' || input.checked)
            .map((input) => input.value);

        if (primarySelect.options) {
            Array.from(primarySelect.options).forEach((option) => {
                option.hidden = option.value ? !selecteds.includes(option.value) : false;
            });
        }

        if (primarySelect.value && !selecteds.includes(primarySelect.value)) {
            primarySelect.value = '';
            primarySelect.dispatchEvent(new Event('input', { bubbles: true }));
            primarySelect.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function collectTempleFields() {
        const form = getTempleForm();
        const data = {};

        if (!form) {
            return data;
        }

        form.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
            if (field.type === 'hidden'
                && ! field.matches('[data-rich-editor-input]')
                && ! ['cover_media_id', 'gallery_media_ids[]'].includes(field.name)
            ) {
                return;
            }

            if (field.name.endsWith('[]')) {
                data[field.name] = data[field.name] || [];

                if (field.type !== 'checkbox' || field.checked) {
                    data[field.name].push(field.value);
                }

                return;
            }

            if (field.type === 'checkbox') {
                data[field.name] = field.checked;
                return;
            }

            if (field.type === 'radio') {
                if (field.checked) {
                    data[field.name] = field.value;
                }

                return;
            }

            data[field.name] = field.value;
        });

        return data;
    }

    function saveTempleDraft() {
        window.templeDraft.write({
            fields: collectTempleFields(),
        });
    }

    function restoreTempleFields() {
        const form = getTempleForm();
        const fields = window.templeDraft.get('fields', {});

        if (!form || !fields || window.templeFormHasServerErrors) {
            return;
        }

        Object.entries(fields).forEach(([name, value]) => {
            const elements = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);

            elements.forEach((field) => {
                if (field.type === 'checkbox') {
                    field.checked = Array.isArray(value)
                        ? value.includes(field.value)
                        : Boolean(value);

                    field.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                if (field.type === 'radio') {
                    field.checked = String(field.value) === String(value);
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                field.value = value ?? '';
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));

                if (field.matches('[data-rich-editor-input]') && field._quill) {
                    field._quill.root.innerHTML = value ?? '';
                    const sourceEditor = field
                        .closest('[data-rich-editor]')
                        ?.querySelector('[data-editor-source]');

                    if (sourceEditor) {
                        sourceEditor.value = value ?? '';
                    }
                }
            });
        });

        updatePrimaryCategoryOptions();
    }

    function bindTempleDraftEvents() {
        const form = getTempleForm();

        if (!form || form.dataset.draftBound === 'true') {
            return;
        }

        form.dataset.draftBound = 'true';

        form.addEventListener('input', saveTempleDraft, true);
        form.addEventListener('change', () => {
            updatePrimaryCategoryOptions();
            saveTempleDraft();
        }, true);

        form.addEventListener('submit', () => {
            window.templeDraft.clear();
        });

        window.addEventListener('beforeunload', saveTempleDraft);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initTempleRichEditors();
        restoreTempleFields();
        bindTempleDraftEvents();
    });

    document.addEventListener('alpine:load', () => {
        setTimeout(() => {
            window.Alpine.nextTick(() => {
                initTempleRichEditors();
                restoreTempleFields();
                bindTempleDraftEvents();
                saveTempleDraft();
            });
        }, 100);
    });

    function initTempleRichEditors() {
        if (! window.Quill) {
            return;
        }

        registerTempleRichTextFormats();

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

            if (! input || ! editorBody || ! toolbar) {
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
    }

    function initTempleInlineRichEditor(wrapper, row, field) {
        if (! window.Quill || ! wrapper || wrapper.dataset.richEditorBound === 'true') {
            return;
        }

        registerTempleRichTextFormats();

        const editorBody = wrapper.querySelector('[data-editor-body]');
        const toolbar = wrapper.querySelector('[data-editor-toolbar]');

        if (! editorBody || ! toolbar) {
            return;
        }

        wrapper.dataset.richEditorBound = 'true';

        const quill = new Quill(editorBody, {
            theme: 'snow',
            placeholder: wrapper.dataset.placeholder || '',
            modules: {
                toolbar,
            },
            formats: ['bold', 'italic', 'lineheight', 'list', 'link', 'underline'],
        });

        quill.root.innerHTML = row[field] || '';

        quill.on('text-change', () => {
            const html = quill.root.innerHTML.trim();
            row[field] = html === '<p><br></p>' ? '' : html;
            saveTempleDraft();
        });
    }

    function registerTempleRichTextFormats() {
        if (window.templeRichTextFormatsRegistered || ! window.Quill) {
            return;
        }

        const Parchment = window.Quill.import('parchment');
        const LineHeight = new Parchment.ClassAttributor('lineheight', 'ql-lineheight', {
            scope: Parchment.Scope.BLOCK,
            whitelist: ['tight', 'normal', 'relaxed', 'loose'],
        });

        window.Quill.register(LineHeight, true);
        window.templeRichTextFormatsRegistered = true;
    }

    function repeaterManager(prefix, initialRows = []) {
        return {
            rows: [],

            init() {
                const fallbackRows = initialRows.length ? initialRows : [];
                const rows = window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get(prefix, fallbackRows);

                this.rows = rows.map((row) => ({
                    _key: row._key || `${prefix}-${Date.now()}-${Math.random().toString(36).slice(2)}`,
                    ...row,
                }));

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        [prefix]: value,
                    });
                });
            },

            addRow(defaults = {}) {
                this.rows.push({
                    _key: `${prefix}-${Date.now()}-${Math.random().toString(36).slice(2)}`,
                    ...defaults,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },
        };
    }

    function openingHoursManager() {
        const existing = @json($jsOpeningHours);

        return {
            dayNames: @json(array_values($days)),
            rows: [],

            init() {
                const defaultRows = existing.length
                    ? this.compactRows(existing)
                    : [
                        {
                            preset: 'everyday',
                            day_from: 0,
                            day_to: 6,
                            open_time: '08:00',
                            close_time: '16:00',
                            note: '',
                            is_closed: false,
                        },
                    ];

                this.rows = window.templeFormHasServerErrors
                    ? defaultRows
                    : window.templeDraft.get('opening_hours_rows', defaultRows);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        opening_hours_rows: value,
                    });
                });
            },

            compactRows(rows) {
                const normalizedRows = rows
                    .map((row) => ({
                        day: Number(row.day_of_week ?? 0),
                        open_time: row.open_time || '',
                        close_time: row.close_time || '',
                        is_closed: Boolean(row.is_closed),
                        note: row.note || '',
                    }))
                    .filter((row) => row.day >= 0 && row.day <= 6)
                    .sort((a, b) => a.day - b.day);

                const groupedRows = [];

                normalizedRows.forEach((row) => {
                    const last = groupedRows[groupedRows.length - 1];
                    const hasSames = last
                        && last.day_to + 1 === row.day
                        && last.open_time === row.open_time
                        && last.close_time === row.close_time
                        && last.is_closed === row.is_closed
                        && last.note === row.note;

                    if (hasSames) {
                        last.day_to = row.day;
                        last.preset = this.detectPreset(last.day_from, last.day_to);
                        return;
                    }

                    groupedRows.push({
                        preset: 'oneday',
                        day_from: row.day,
                        day_to: row.day,
                        open_time: row.open_time,
                        close_time: row.close_time,
                        is_closed: row.is_closed,
                        note: row.note,
                    });
                });

                const first = groupedRows[0];
                const last = groupedRows[groupedRows.length - 1];

                if (
                    groupedRows.length > 1
                    && first.day_from === 0
                    && first.day_to === 0
                    && last.day_from === 6
                    && last.day_to === 6
                    && first.open_time === last.open_time
                    && first.close_time === last.close_time
                    && first.is_closed === last.is_closed
                    && first.note === last.note
                ) {
                    last.day_to = 0;
                    groupedRows.shift();
                }

                return groupedRows.map((row) => ({
                    ...row,
                    preset: this.detectPreset(row.day_from, row.day_to),
                }));
            },

            detectPreset(from, to) {
                if (from === 0 && to === 6) {
                    return 'everyday';
                }

                if (from === 1 && to === 5) {
                    return 'weekdays';
                }

                if (from === 6 && to === 0) {
                    return 'weekend';
                }

                return from === to ? 'oneday' : 'custom';
            },

            addRow() {
                this.rows.push({
                    preset: 'weekdays',
                    day_from: 1,
                    day_to: 5,
                    open_time: '08:00',
                    close_time: '16:00',
                    note: '',
                    is_closed: false,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },

            applyPreset(row) {
                if (row.preset === 'everyday') {
                    row.day_from = 0;
                    row.day_to = 6;
                }

                if (row.preset === 'weekdays') {
                    row.day_from = 1;
                    row.day_to = 5;
                }

                if (row.preset === 'weekend') {
                    row.day_from = 6;
                    row.day_to = 0;
                }

                if (row.preset === 'oneday') {
                    row.day_to = row.day_from;
                }
            },

            getDaysInRange(from, to) {
                const days = [];

                if (from <= to) {
                    for (let day = from; day <= to; day++) {
                        days.push(day);
                    }

                    return days;
                }

                for (let day = from; day <= 6; day++) {
                    days.push(day);
                }

                for (let day = 0; day <= to; day++) {
                    days.push(day);
                }

                return days;
            },

            previewDays(row) {
                const days = this.getDaysInRange(Number(row.day_from), Number(row.day_to));

                if (days.length === 1) {
                    return this.dayNames[days[0]];
                }

                return days.map((day) => this.dayNames[day]).join(', ');
            },

            expandedRows() {
                const itemsByDay = {};

                this.rows.forEach((row) => {
                    this.getDaysInRange(Number(row.day_from), Number(row.day_to)).forEach((day) => {
                        itemsByDay[day] = {
                            day_of_week: day,
                            open_time: row.open_time,
                            close_time: row.close_time,
                            note: row.note,
                            is_closed: row.is_closed,
                        };
                    });
                });

                return Object.values(itemsByDay).sort((a, b) => a.day_of_week - b.day_of_week);
            },
        };
    }

    function feesManager() {
        const existing = @json($jsFees);

        return {
            rows: [],

            init() {
                const fallbackRows = existing.length ? existing : [];
                this.rows = window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get('fees_rows', fallbackRows);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        fees_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    fee_type: '',
                    label: '',
                    amount: '',
                    currency: 'THB',
                    note: '',
                    is_active: true,
                    sort_order: this.rows.length,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },
        };
    }

    function facilitiesManager() {
        const existing = @json($jsFacilityItems);
        const facilitiesUrl = @json(route('admin.lookups.facilities'));

        return {
            rows: [],

            init() {
                const fallbackRows = existing.length ? existing : [];
                this.rows = (window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get('facility_items_rows', fallbackRows)
                ).map((row) => ({
                    _key: row._key || `facility-${Date.now()}-${Math.random().toString(36).slice(2)}`,
                    facility_id: row.facility_id ? String(row.facility_id) : '',
                    facility_search: row.facility_search || '',
                    facility_name: row.facility_name || '',
                    facility_options: row.facility_id && row.facility_name ? [{
                        id: String(row.facility_id),
                        label: row.facility_name,
                    }] : [],
                    value: row.value || '',
                    note: row.note || '',
                    sort_order: row.sort_order ?? 0,
                }));

                this.rows.forEach((row) => this.searchFacilities(row));

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        facility_items_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    _key: `facility-${Date.now()}-${Math.random().toString(36).slice(2)}`,
                    facility_id: '',
                    facility_search: '',
                    facility_name: '',
                    facility_options: [],
                    value: '',
                    note: '',
                    sort_order: this.rows.length,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },

            filteredFacilities(row) {
                return row.facility_options || [];
            },

            async searchFacilities(row) {
                const url = new URL(facilitiesUrl, window.location.origin);

                if (row.facility_search?.trim()) {
                    url.searchParams.set('q', row.facility_search.trim());
                }

                if (row.facility_id) {
                    url.searchParams.append('ids[]', row.facility_id);
                }

                const response = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                row.facility_options = (payload.items || []).map((item) => ({
                    id: String(item.id),
                    name: item.label,
                }));
            },
        };
    }

    function travelInfosManager() {
        const existing = @json($jsTravelInfos);

        return {
            rows: [],

            init() {
                const fallbackRows = existing.length ? existing : [];
                this.rows = window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get('travel_infos_rows', fallbackRows);

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        travel_infos_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    travel_type: '',
                    start_place: '',
                    distance_km: '',
                    duration_minutes: '',
                    cost_estimate: '',
                    note: '',
                    is_active: true,
                    sort_order: this.rows.length,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },
        };
    }

    function nearbyPlacesManager() {
        const existing = @json($jsNearbyPlaces);
        const templesUrl = @json(route('admin.lookups.temples', array_filter(['exclude_id' => $temple?->id])));

        return {
            rows: [],

            init() {
                const fallbackRows = existing.length ? existing : [];
                this.rows = window.templeFormHasServerErrors
                    ? fallbackRows
                    : window.templeDraft.get('nearby_places_rows', fallbackRows);
                this.rows = this.rows.map((row) => ({
                    ...row,
                    nearby_temple_id: row.nearby_temple_id ? String(row.nearby_temple_id) : '',
                    temple_search: row.temple_search || '',
                    temple_options: row.nearby_temple_id && row.nearby_temple_title ? [{
                        id: String(row.nearby_temple_id),
                        title: row.nearby_temple_title,
                    }] : [],
                }));

                this.rows.forEach((row) => this.searchNearbyTemples(row));

                this.$watch('rows', (value) => {
                    window.templeDraft.write({
                        nearby_places_rows: value,
                    });
                });
            },

            addRow() {
                this.rows.push({
                    nearby_temple_id: '',
                    temple_search: '',
                    temple_options: [],
                    relation_type: '',
                    distance_km: '',
                    duration_minutes: '',
                    score: '',
                    sort_order: this.rows.length,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },

            filteredNearbyTemples(row) {
                return row.temple_options || [];
            },

            async searchNearbyTemples(row) {
                const url = new URL(templesUrl, window.location.origin);

                if (row.temple_search?.trim()) {
                    url.searchParams.set('q', row.temple_search.trim());
                }

                if (row.nearby_temple_id) {
                    url.searchParams.append('ids[]', row.nearby_temple_id);
                }

                const response = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                row.temple_options = (payload.items || []).map((item) => ({
                    id: String(item.id),
                    title: item.label,
                }));
            },
        };
    }
</script>
