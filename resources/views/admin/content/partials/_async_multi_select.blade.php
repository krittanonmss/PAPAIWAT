@php
    $fieldId = $id ?? \Illuminate\Support\Str::slug($name ?? 'async-multi-select') . '-' . uniqid();
    $fieldName = $name ?? 'items';
    $label = $label ?? 'เลือกข้อมูล';
    $placeholder = $placeholder ?? 'พิมพ์เพื่อค้นหา...';
    $emptyText = $emptyText ?? 'ยังไม่มีรายการที่เลือก';
    $noResultsText = $noResultsText ?? 'ไม่พบรายการที่ตรงกับคำค้นหา';
    $searchUrl = $searchUrl ?? '#';
    $selectedIds = collect($selectedIds ?? [])
        ->filter(fn ($id) => is_scalar($id) && (string) $id !== '')
        ->map(fn ($id) => (string) $id)
        ->unique()
        ->values();
    $selectedOptions = collect($selectedOptions ?? [])
        ->map(function ($option) {
            return [
                'id' => (string) ($option['id'] ?? $option['value'] ?? ''),
                'label' => (string) ($option['label'] ?? $option['name'] ?? ''),
                'meta' => (string) ($option['meta'] ?? ''),
            ];
        })
        ->filter(fn ($option) => $option['id'] !== '' && $option['label'] !== '')
        ->values();
@endphp

<div
    x-data="asyncMultiSelect({
        url: @js($searchUrl),
        selectedIds: @js($selectedIds),
        selectedOptions: @js($selectedOptions),
    })"
    x-init="init()"
    class="space-y-4"
>
    <div>
        <label for="{{ $fieldId }}_search" class="mb-1.5 block text-sm font-medium text-slate-300">
            {{ $label }}
        </label>
        <input
            id="{{ $fieldId }}_search"
            type="search"
            x-model="search"
            @input.debounce.250ms="searchOptions()"
            placeholder="{{ $placeholder }}"
            class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
        >
    </div>

    <template x-for="id in selectedIds" :key="id">
        <input type="hidden" name="{{ $fieldName }}[]" :value="id">
    </template>

    <div class="flex items-center justify-between rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3">
        <p class="text-xs text-slate-400">
            เลือกแล้ว
            <span class="font-semibold text-blue-300" x-text="selectedIds.length"></span>
            รายการ
        </p>

        <button
            type="button"
            x-show="selectedIds.length > 0"
            @click="clear()"
            class="text-xs font-medium text-rose-300 transition hover:text-rose-200"
        >
            ล้างทั้งหมด
        </button>
    </div>

    <div x-show="selectedList.length > 0" class="flex flex-wrap gap-2">
        <template x-for="option in selectedList" :key="option.id">
            <button
                type="button"
                @click="remove(option.id)"
                class="inline-flex max-w-full items-center gap-2 rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1.5 text-xs text-blue-100 transition hover:bg-blue-500/20"
            >
                <span class="truncate" x-text="option.label"></span>
                <span class="text-blue-300">×</span>
            </button>
        </template>
    </div>

    <p x-show="selectedList.length === 0" class="rounded-xl border border-dashed border-white/10 px-3 py-3 text-center text-xs text-slate-500">
        {{ $emptyText }}
    </p>

    <div class="max-h-[420px] space-y-3 overflow-y-auto pr-1">
        <template x-for="option in results" :key="option.id">
            <button
                type="button"
                @click="toggle(option)"
                class="flex w-full cursor-pointer items-start gap-3 rounded-xl border p-4 text-left transition"
                :class="isSelected(option.id)
                    ? 'border-blue-400/50 bg-blue-500/10 ring-1 ring-blue-500/20'
                    : 'border-white/10 bg-slate-950/40 hover:border-blue-400/30 hover:bg-white/5'"
            >
                <span
                    class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded border text-[10px]"
                    :class="isSelected(option.id) ? 'border-blue-400 bg-blue-500 text-white' : 'border-white/20 bg-slate-950 text-transparent'"
                >✓</span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-medium text-slate-200" x-text="option.label"></span>
                    <span x-show="option.meta" class="mt-1 block text-xs text-slate-500" x-text="option.meta"></span>
                </span>
                <span
                    x-show="isSelected(option.id)"
                    class="shrink-0 rounded-full border border-blue-400/20 bg-blue-500/10 px-2.5 py-1 text-[11px] font-medium text-blue-300"
                >
                    เลือกแล้ว
                </span>
            </button>
        </template>

        <div x-show="loading" class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-6 text-center text-sm text-slate-500">
            กำลังค้นหา...
        </div>

        <div
            x-show="!loading && results.length === 0"
            class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-6 text-center text-sm text-slate-500"
        >
            {{ $noResultsText }}
        </div>
    </div>
</div>

@once
    <script>
        function asyncMultiSelect(config) {
            return {
                url: config.url,
                search: '',
                loading: false,
                selectedIds: (config.selectedIds || []).map(String),
                selectedOptions: config.selectedOptions || [],
                results: [],

                init() {
                    this.selectedOptions.forEach((option) => {
                        option.id = String(option.id);
                    });

                    this.fetchSelected();
                    this.searchOptions();
                },

                get selectedList() {
                    return this.selectedIds
                        .map((id) => this.selectedOptions.find((option) => String(option.id) === String(id)))
                        .filter(Boolean);
                },

                isSelected(id) {
                    return this.selectedIds.includes(String(id));
                },

                optionKnown(id) {
                    return this.selectedOptions.some((option) => String(option.id) === String(id));
                },

                normalizeItems(items) {
                    return (items || []).map((item) => ({
                        id: String(item.id),
                        label: item.label || item.name || `#${item.id}`,
                        meta: item.meta || '',
                    }));
                },

                mergeOptions(items) {
                    this.normalizeItems(items).forEach((item) => {
                        const index = this.selectedOptions.findIndex((option) => option.id === item.id);

                        if (index >= 0) {
                            this.selectedOptions[index] = item;
                        } else {
                            this.selectedOptions.push(item);
                        }
                    });
                },

                async fetchSelected() {
                    const missingIds = this.selectedIds.filter((id) => !this.optionKnown(id));

                    if (missingIds.length === 0) {
                        return;
                    }

                    const url = new URL(this.url, window.location.origin);
                    missingIds.forEach((id) => url.searchParams.append('ids[]', id));

                    const response = await fetch(url, {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });

                    if (response.ok) {
                        const payload = await response.json();
                        this.mergeOptions(payload.items || []);
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
                            this.mergeOptions(this.results);
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                toggle(option) {
                    const id = String(option.id);
                    this.mergeOptions([option]);

                    if (this.isSelected(id)) {
                        this.remove(id);
                    } else {
                        this.selectedIds.push(id);
                    }
                },

                remove(id) {
                    id = String(id);
                    this.selectedIds = this.selectedIds.filter((item) => item !== id);
                },

                clear() {
                    this.selectedIds = [];
                },
            };
        }
    </script>
@endonce
