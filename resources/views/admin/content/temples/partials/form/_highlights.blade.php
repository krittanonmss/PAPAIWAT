    {{-- Section: Highlights --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="repeaterManager('highlights', @json($jsHighlights))"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">จุดเด่นของ</h2>
                <p class="mt-1 text-xs text-slate-400">ไฮไลต์สำคัญที่ใช้แสดงในหน้ารายละเอียด</p>
            </div>

            <button
                type="button"
                @click="addRow({ title: '', description: '', sort_order: rows.length })"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มจุดเด่น
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="row._key || index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- หัวข้อ --}}
                        <div class="col-span-12 md:col-span-5">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                ชื่อจุดเด่น <span class="text-rose-400">*</span>
                            </label>

                            <input
                                type="text"
                                :name="`highlights[${index}][title]`"
                                x-model="row.title"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น พระพุทธรูปสำคัญ"
                            >
                        </div>

                        {{-- ลำดับ --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ลำดับ</label>

                            <input
                                type="number"
                                :name="`highlights[${index}][sort_order]`"
                                x-model="row.sort_order"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                                min="0"
                            >
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block text-xs font-medium text-slate-400">รายละเอียด</label>

                            <input
                                type="hidden"
                                :name="`highlights[${index}][description]`"
                                x-model="row.description"
                            >

                            <div
                                class="overflow-hidden rounded-xl border border-white/10 bg-slate-950/70 transition focus-within:border-blue-400"
                                data-inline-rich-editor
                                data-placeholder="รายละเอียดเพิ่มเติมของจุดเด่นนี้"
                                x-init="$nextTick(() => initTempleInlineRichEditor($el, row, 'description'))"
                            >
                                <div data-editor-toolbar class="temple-editor-toolbar temple-editor-toolbar-compact border-b border-white/10 bg-slate-900/90 px-2 py-1.5">
                                    <span class="ql-formats">
                                        <select class="ql-lineheight" title="Line spacing">
                                            <option selected></option>
                                            <option value="tight"></option>
                                            <option value="relaxed"></option>
                                            <option value="loose"></option>
                                        </select>
                                    </span>
                                    <span class="ql-formats">
                                        <button type="button" class="ql-bold" title="Bold"></button>
                                        <button type="button" class="ql-italic" title="Italic"></button>
                                        <button type="button" class="ql-underline" title="Underline"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button type="button" class="ql-list" value="bullet" title="Bullet list"></button>
                                        <button type="button" class="ql-link" title="Link"></button>
                                        <button type="button" class="ql-clean" title="Clear formatting"></button>
                                    </span>
                                </div>
                                <div data-editor-body class="temple-rich-editor px-4 py-3 text-sm leading-6 text-slate-100" style="min-height: 130px"></div>
                            </div>
                        </div>

                        {{-- Remove --}}
                        <div class="col-span-12 flex justify-end">
                            <button
                                type="button"
                                @click="removeRow(index)"
                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                            >
                                ✕ ลบรายการนี้
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <p x-show="rows.length === 0" class="text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มจุดเด่นเพื่อเพิ่ม
            </p>
        </div>
    </section>
