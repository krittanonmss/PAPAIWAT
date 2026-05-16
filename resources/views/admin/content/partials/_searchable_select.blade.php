@php
    $selectId = $id ?? \Illuminate\Support\Str::slug($name ?? 'searchable-select') . '-' . uniqid();
    $selectName = $name ?? $selectId;
    $selected = (string) ($selected ?? '');
    $allowEmpty = $allowEmpty ?? true;
    $emptyLabel = $emptyLabel ?? 'ไม่เลือก';
    $placeholder = $placeholder ?? 'ค้นหาและเลือก';
    $searchPlaceholder = $searchPlaceholder ?? 'พิมพ์เพื่อค้นหา...';
    $errorKey = $errorKey ?? $selectName;
    $inputClass = $inputClass ?? 'w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
    $panelClass = $panelClass ?? 'absolute z-[80] max-h-64 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/95 p-1 shadow-2xl shadow-slate-950/60 backdrop-blur';
    $dataAttributes = $dataAttributes ?? [];
    $visibleLimit = $visibleLimit ?? 80;
    $normalizedOptions = collect($options ?? [])->map(function ($option) {
        if (is_array($option)) {
            return [
                'value' => (string) ($option['value'] ?? ''),
                'label' => (string) ($option['label'] ?? ''),
                'meta' => (string) ($option['meta'] ?? ''),
                'search' => mb_strtolower((string) ($option['search'] ?? (($option['label'] ?? '') . ' ' . ($option['meta'] ?? '') . ' ' . ($option['value'] ?? '')))),
            ];
        }

        return [
            'value' => (string) data_get($option, 'id', ''),
            'label' => (string) data_get($option, 'name', data_get($option, 'title', '')),
            'meta' => '',
            'search' => mb_strtolower((string) (data_get($option, 'name', data_get($option, 'title', '')) . ' ' . data_get($option, 'id', ''))),
        ];
    })->filter(fn ($option) => $option['value'] !== '' && $option['label'] !== '')->values();
@endphp

<div
    class="relative"
    x-data="{
        open: false,
        dropUp: false,
        search: '',
        value: @js($selected),
        options: @js($normalizedOptions),
        visibleLimit: @js($visibleLimit),
        get selectedOption() {
            return this.options.find((option) => option.value === String(this.value));
        },
        get displayLabel() {
            return this.selectedOption ? this.selectedOption.label : @js($allowEmpty ? $emptyLabel : $placeholder);
        },
        get filteredOptions() {
            const keyword = this.search.toLowerCase().trim();
            const matches = keyword
                ? this.options.filter((option) => option.search.includes(keyword))
                : this.options;

            if (!this.visibleLimit) {
                return matches;
            }

            if (!keyword) {
                return matches.slice(0, this.visibleLimit);
            }

            return matches.slice(0, this.visibleLimit);
        },
        normalizeValue(value) {
            if (value instanceof HTMLElement) {
                return value.value || '';
            }

            return String(value || '');
        },
        choose(value) {
            this.value = this.normalizeValue(value);
            this.open = false;
            this.search = '';
            this.$nextTick(() => {
                this.$refs.valueInput.dispatchEvent(new Event('input', { bubbles: true }));
                this.$refs.valueInput.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },
        toggle() {
            this.open = !this.open;

            if (this.open) {
                this.$nextTick(() => {
                    const rect = this.$refs.triggerButton.getBoundingClientRect();
                    this.dropUp = window.innerHeight - rect.bottom < 320 && rect.top > 320;
                    this.$refs.searchInput?.focus();
                });
            }
        }
    }"
    @click.outside="open = false"
>
    <input
        type="hidden"
        id="{{ $selectId }}"
        name="{{ $selectName }}"
        x-ref="valueInput"
        x-model="value"
        @foreach ($dataAttributes as $attribute => $attributeValue)
            @if ($attributeValue === null || $attributeValue === '')
                {{ $attribute }}
            @else
                {{ $attribute }}="{{ $attributeValue }}"
            @endif
        @endforeach
    >

    <button
        type="button"
        x-ref="triggerButton"
        class="{{ $inputClass }} flex min-h-[2.75rem] items-center justify-between gap-3 text-left @error($errorKey) border-rose-400/60 @enderror"
        @click="toggle()"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
    >
        <span class="min-w-0 truncate" :class="selectedOption ? 'text-white' : 'text-slate-500'" x-text="displayLabel"></span>
        <span class="shrink-0 text-slate-500">⌄</span>
    </button>

    <div
        x-show="open"
        x-cloak
        class="{{ $panelClass }}"
        :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-2'"
        role="listbox"
    >
        <div class="p-2">
            <input
                type="search"
                x-ref="searchInput"
                x-model.debounce.100ms="search"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
            >
        </div>

        @if ($allowEmpty)
            <button
                type="button"
                class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm text-slate-300 transition hover:bg-white/[0.06]"
                @click="choose('')"
            >
                <span>{{ $emptyLabel }}</span>
                <span x-show="value === ''" class="text-blue-300">เลือกแล้ว</span>
            </button>
        @endif

        <template x-for="option in filteredOptions" :key="option.value">
            <button
                type="button"
                class="flex w-full items-start justify-between gap-3 rounded-xl px-3 py-2.5 text-left text-sm transition hover:bg-white/[0.06]"
                :class="value === option.value ? 'bg-blue-500/10 text-blue-100' : 'text-slate-300'"
                @click="choose(option.value)"
            >
                <span class="min-w-0">
                    <span class="block truncate font-medium" x-text="option.label"></span>
                    <span x-show="option.meta" class="mt-0.5 block truncate text-xs text-slate-500" x-text="option.meta"></span>
                </span>
                <span x-show="value === option.value" class="shrink-0 text-xs text-blue-300">เลือกแล้ว</span>
            </button>
        </template>

        <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-sm text-slate-500">
            ไม่พบรายการที่ตรงกับคำค้นหา
        </div>
    </div>
</div>
