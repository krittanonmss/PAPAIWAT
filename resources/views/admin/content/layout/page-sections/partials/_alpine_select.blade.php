@php
    $path = $path ?? '';
    $options = collect($options ?? [])->map(fn ($option) => [
        'value' => (string) ($option['value'] ?? ''),
        'label' => (string) ($option['label'] ?? ''),
        'meta' => (string) ($option['meta'] ?? ''),
        'search' => mb_strtolower((string) ($option['search'] ?? (($option['label'] ?? '').' '.($option['meta'] ?? '').' '.($option['value'] ?? '')))),
    ])->values();
    $placeholder = $placeholder ?? 'เลือก';
    $searchPlaceholder = $searchPlaceholder ?? 'ค้นหา...';
    $afterChoose = $afterChoose ?? '';
    $buttonClass = $buttonClass ?? 'w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
@endphp

<div
    class="relative"
    :class="open ? 'z-[90]' : 'z-0'"
    x-data="{
        open: false,
        search: '',
        options: @js($options),
        get selectedOption() {
            return this.options.find((option) => option.value === String(sectionGet(@js($path)) || ''));
        },
        get displayLabel() {
            return this.selectedOption ? this.selectedOption.label : @js($placeholder);
        },
        get filteredOptions() {
            const keyword = this.search.toLowerCase().trim();
            return (keyword ? this.options.filter((option) => option.search.includes(keyword)) : this.options).slice(0, 80);
        },
        choose(value) {
            sectionSet(@js($path), value);
            this.open = false;
            this.search = '';
            @if ($afterChoose !== '')
                const selectedValue = value;
                {!! $afterChoose !!}
            @endif
        },
    }"
    @click.outside="open = false"
>
    <button
        type="button"
        class="{{ $buttonClass }} flex min-h-[2.75rem] items-center justify-between gap-3 text-left"
        @click="open = !open; if (open) $nextTick(() => $refs.searchInput?.focus())"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
    >
        <span class="min-w-0 truncate" :class="selectedOption ? 'text-white' : 'text-slate-500'" x-text="displayLabel"></span>
        <span class="shrink-0 text-slate-500">⌄</span>
    </button>

    <div
        x-show="open"
        x-cloak
        class="absolute top-full z-[80] mt-2 max-h-72 w-full overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/95 p-1 shadow-2xl shadow-slate-950/60 backdrop-blur"
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

        <template x-for="option in filteredOptions" :key="option.value">
            <button
                type="button"
                class="flex w-full items-start justify-between gap-3 rounded-xl px-3 py-2.5 text-left text-sm transition hover:bg-white/[0.06]"
                :class="String(sectionGet(@js($path)) || '') === option.value ? 'bg-blue-500/10 text-blue-100' : 'text-slate-300'"
                @click="choose(option.value)"
            >
                <span class="min-w-0">
                    <span class="block truncate font-medium" x-text="option.label"></span>
                    <span x-show="option.meta" class="mt-0.5 block truncate text-xs text-slate-500" x-text="option.meta"></span>
                </span>
                <span x-show="String(sectionGet(@js($path)) || '') === option.value" class="shrink-0 text-xs text-blue-300">เลือกแล้ว</span>
            </button>
        </template>

        <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-sm text-slate-500">
            ไม่พบรายการที่ตรงกับคำค้นหา
        </div>
    </div>
</div>
