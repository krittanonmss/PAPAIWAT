@props([
    'name',
    'id',
    'label',
    'value' => '',
    'placeholder' => '',
    'errorชื่อ' => null,
    'minHeight' => '260px',
    'maxHeight' => null,
    'hint' => null,
    'disabledExpression' => null,
])

@php
    $errorชื่อ = $errorชื่อ ?? $name;
@endphp

<div data-rich-editor data-placeholder="{{ $placeholder }}" class="space-y-2">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-300">{{ $label }}</label>

        @if ($hint)
            <p class="text-xs leading-5 text-slate-500">{{ $hint }}</p>
        @endif
    </div>

    <input
        type="hidden"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        data-rich-editor-input
        @if ($disabledExpression) :disabled="{{ $disabledExpression }}" @endif
    >

    <div class="temple-editor-shell overflow-hidden rounded-2xl border border-white/10 bg-slate-950/70 shadow-inner shadow-slate-950/40 transition focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-500/20 @error($errorชื่อ) border-rose-400 @enderror">
        <div data-editor-toolbar class="temple-editor-toolbar border-b border-white/10 bg-slate-900/95 px-3 py-2">
            <span class="ql-formats">
                <select class="ql-header" title="หัวข้อ">
                    <option selected></option>
                    <option value="1"></option>
                    <option value="2"></option>
                    <option value="3"></option>
                </select>
                <select class="ql-lineheight" title="ระยะห่างบรรทัด">
                    <option selected></option>
                    <option value="tight"></option>
                    <option value="relaxed"></option>
                    <option value="loose"></option>
                </select>
            </span>

            <span class="ql-formats">
                <button type="button" class="ql-bold" title="ตัวหนา"></button>
                <button type="button" class="ql-italic" title="ตัวเอียง"></button>
                <button type="button" class="ql-underline" title="ขีดเส้นใต้"></button>
                <button type="button" class="ql-strike" title="ขีดฆ่า"></button>
            </span>

            <span class="ql-formats">
                <button type="button" class="ql-list" value="ordered" title="รายการลำดับเลข"></button>
                <button type="button" class="ql-list" value="bullet" title="รายการหัวข้อ"></button>
                <button type="button" class="ql-indent" value="-1" title="ลดระยะเยื้อง"></button>
                <button type="button" class="ql-indent" value="+1" title="เพิ่มระยะเยื้อง"></button>
            </span>

            <span class="ql-formats">
                <button type="button" class="ql-blockquote" title="คำอ้างอิง"></button>
                <button type="button" class="ql-code-block" title="บล็อกโค้ด"></button>
                <button type="button" class="ql-link" title="ลิงก์"></button>
            </span>

            <span class="ql-formats">
                <button type="button" class="ql-script" value="sub" title="ตัวห้อย"></button>
                <button type="button" class="ql-script" value="super" title="ตัวยก"></button>
                <button type="button" class="ql-clean" title="ล้างรูปแบบ"></button>
            </span>

            <span class="ql-formats">
                <button type="button" data-editor-source-toggle class="px-2 text-xs font-semibold text-slate-300" title="แก้ไข HTML">HTML</button>
            </span>
        </div>

        <div
            data-editor-body
            class="temple-rich-editor max-w-none overflow-y-auto px-5 py-4 text-base leading-7 text-slate-100"
            style="min-height: {{ $minHeight }}; @if ($maxHeight) max-height: {{ $maxHeight }}; @endif"
        ></div>

        <textarea
            data-editor-source
            class="hidden w-full resize-none overflow-y-auto border-0 bg-slate-950 px-5 py-4 font-mono text-sm leading-7 text-slate-100 outline-none"
            spellcheck="false"
            style="min-height: {{ $minHeight }}; @if ($maxHeight) max-height: {{ $maxHeight }}; @endif"
        >{{ old($name, $value) }}</textarea>

        <div class="flex items-center justify-between gap-3 border-t border-white/10 bg-slate-950/50 px-4 py-2 text-xs text-slate-500">
            <span data-editor-mode-label>ตัวแก้ไขข้อความ</span>
            <span data-editor-count>0 ตัวอักษร</span>
        </div>
    </div>

    @error($errorชื่อ)
        <p class="text-xs text-rose-300">{{ $message }}</p>
    @enderror
</div>
