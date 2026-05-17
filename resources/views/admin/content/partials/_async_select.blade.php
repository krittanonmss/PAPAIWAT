@php
    $fieldId = $id ?? \Illuminate\Support\Str::slug($name ?? 'async-select') . '-' . uniqid();
    $fieldName = $name ?? $fieldId;
    $selected = (string) ($selected ?? '');
    $selectedOption = $selectedOption ?? null;
    $label = $label ?? null;
    $placeholder = $placeholder ?? 'ค้นหาและเลือก';
    $searchPlaceholder = $searchPlaceholder ?? 'พิมพ์เพื่อค้นหา...';
    $emptyLabel = $emptyLabel ?? 'ไม่เลือก';
    $searchUrl = $searchUrl ?? '#';
    $initialOption = $selectedOption ? [
        'id' => (string) ($selectedOption['id'] ?? $selectedOption['value'] ?? ''),
        'label' => (string) ($selectedOption['label'] ?? $selectedOption['name'] ?? ''),
        'meta' => (string) ($selectedOption['meta'] ?? ''),
    ] : null;
@endphp

<div
    class="relative"
    x-data="asyncSelect({
        url: @js($searchUrl),
        value: @js($selected),
        option: @js($initialOption),
        emptyLabel: @js($emptyLabel),
        placeholder: @js($placeholder),
    })"
    x-init="init()"
    @click.outside="open = false"
>
    @if ($label)
        <label for="{{ $fieldId }}_button" class="mb-1.5 block text-sm font-medium text-slate-300">{{ $label }}</label>
    @endif

    <input type="hidden" id="{{ $fieldId }}" name="{{ $fieldName }}" x-model="value">

    <button
        id="{{ $fieldId }}_button"
        type="button"
        @click="toggle()"
        class="flex min-h-[2.75rem] w-full items-center justify-between gap-3 rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-left text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
    >
        <span class="min-w-0 truncate" :class="selectedOption ? 'text-white' : 'text-slate-500'" x-text="displayLabel"></span>
        <span class="shrink-0 text-slate-500">⌄</span>
    </button>

    <div
        x-show="open"
        x-cloak
        class="absolute z-[80] mt-2 max-h-72 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/95 p-1 shadow-2xl shadow-slate-950/60 backdrop-blur"
    >
        <div class="p-2">
            <input
                type="search"
                x-ref="searchInput"
                x-model="search"
                @input.debounce.250ms="searchOptions()"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
            >
        </div>

        <button
            type="button"
            class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm text-slate-300 transition hover:bg-white/[0.06]"
            @click="choose(null)"
        >
            <span>{{ $emptyLabel }}</span>
            <span x-show="value === ''" class="text-blue-300">เลือกแล้ว</span>
        </button>

        <template x-for="option in results" :key="option.id">
            <button
                type="button"
                class="flex w-full items-start justify-between gap-3 rounded-xl px-3 py-2.5 text-left text-sm transition hover:bg-white/[0.06]"
                :class="value === option.id ? 'bg-blue-500/10 text-blue-100' : 'text-slate-300'"
                @click="choose(option)"
            >
                <span class="min-w-0">
                    <span class="block truncate font-medium" x-text="option.label"></span>
                    <span x-show="option.meta" class="mt-0.5 block truncate text-xs text-slate-500" x-text="option.meta"></span>
                </span>
                <span x-show="value === option.id" class="shrink-0 text-xs text-blue-300">เลือกแล้ว</span>
            </button>
        </template>

        <div x-show="loading" class="px-3 py-4 text-center text-sm text-slate-500">กำลังค้นหา...</div>
        <div x-show="!loading && results.length === 0" class="px-3 py-4 text-center text-sm text-slate-500">ไม่พบรายการที่ตรงกับคำค้นหา</div>
    </div>
</div>

@once
    <script>
        function asyncSelect(config) {
            return {
                url: config.url,
                value: String(config.value || ''),
                selectedOption: config.option && config.option.id ? {
                    id: String(config.option.id),
                    label: config.option.label,
                    meta: config.option.meta || '',
                } : null,
                emptyLabel: config.emptyLabel,
                placeholder: config.placeholder,
                open: false,
                search: '',
                loading: false,
                results: [],

                init() {
                    this.fetchSelected();
                    this.searchOptions();
                },

                get displayLabel() {
                    return this.selectedOption ? this.selectedOption.label : (this.value ? this.placeholder : this.emptyLabel);
                },

                normalizeItems(items) {
                    return (items || []).map((item) => ({
                        id: String(item.id),
                        label: item.label || item.name || `#${item.id}`,
                        meta: item.meta || '',
                    }));
                },

                async fetchSelected() {
                    if (!this.value || this.selectedOption) {
                        return;
                    }

                    const url = new URL(this.url, window.location.origin);
                    url.searchParams.append('ids[]', this.value);

                    const response = await fetch(url, {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });

                    if (response.ok) {
                        const payload = await response.json();
                        this.selectedOption = this.normalizeItems(payload.items || [])[0] || null;
                    }
                },

                async searchOptions() {
                    this.loading = true;

                    const url = new URL(this.url, window.location.origin);
                    if (this.search.trim()) {
                        url.searchParams.set('q', this.search.trim());
                    }

                    try {
                        const response = await fetch(url, {
                            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });

                        if (response.ok) {
                            const payload = await response.json();
                            this.results = this.normalizeItems(payload.items || []);
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                toggle() {
                    this.open = !this.open;

                    if (this.open) {
                        this.$nextTick(() => this.$refs.searchInput?.focus());
                    }
                },

                choose(option) {
                    this.selectedOption = option;
                    this.value = option ? String(option.id) : '';
                    this.open = false;
                    this.search = '';
                },
            };
        }
    </script>
@endonce
