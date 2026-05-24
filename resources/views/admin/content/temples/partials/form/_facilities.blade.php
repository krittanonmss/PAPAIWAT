    {{-- Section: Facilities --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="facilitiesManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">สิ่งอำนวยความสะดวก</h2>
                <p class="mt-1 text-xs text-slate-400">เพิ่มสิ่งอำนวยความสะดวกของได้จากหน้านี้โดยตรง</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มสิ่งอำนวยความสะดวก
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="row._key || index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        <div class="col-span-12 md:col-span-4">
                            <label class="mb-1 block text-xs font-medium text-slate-400">เลือกจากรายการเดิม</label>
                            <input
                                type="search"
                                x-model.debounce.100ms="row.facility_search"
                                @input.debounce.250ms="searchFacilities(row)"
                                class="mb-2 w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="ค้นหาสิ่งอำนวยความสะดวก..."
                            >
                            <select
                                :name="`facility_items[${index}][facility_id]`"
                                x-model="row.facility_id"
                                @change="if (row.facility_id) row.facility_name = ''"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                            >
                                <option value="">สร้างรายการใหม่</option>
                                <template x-for="facility in filteredFacilities(row)" :key="facility.id">
                                    <option :value="facility.id" x-text="facility.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-4">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ชื่อรายการใหม่</label>
                            <input
                                type="text"
                                :name="`facility_items[${index}][facility_name]`"
                                x-model="row.facility_name"
                                :disabled="Boolean(row.facility_id)"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400 disabled:opacity-50"
                                placeholder="เช่น ห้องน้ำ, ที่จอดรถ"
                            >
                        </div>

                        <div class="col-span-12 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400"></label>
                            <input
                                type="text"
                                :name="`facility_items[${index}][value]`"
                                x-model="row.value"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น มี, ฟรี"
                            >
                        </div>

                        <div class="col-span-12 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ลำดับ</label>
                            <input
                                type="number"
                                :name="`facility_items[${index}][sort_order]`"
                                x-model="row.sort_order"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                                min="0"
                            >
                        </div>

                        <div class="col-span-12">
                            <label class="mb-1 block text-xs font-medium text-slate-400">หมายเหตุ</label>
                            <input
                                type="text"
                                :name="`facility_items[${index}][note]`"
                                x-model="row.note"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="รายละเอียดเพิ่มเติม"
                            >
                        </div>

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
                ยังไม่มีข้อมูล — กดเพิ่มสิ่งอำนวยความสะดวกเพื่อเพิ่ม
            </p>
        </div>
    </section>
