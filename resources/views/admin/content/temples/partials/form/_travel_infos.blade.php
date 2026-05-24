    {{-- Section: Travel Infos --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="travelInfosManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">ข้อมูลการเดินทาง</h2>
                <p class="mt-1 text-xs text-slate-400">วิธีเดินทาง ระยะทาง ระยะเวลา และใช้จ่ายโดยประมาณ</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มข้อมูลเดินทาง
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- Travel Type --}}
                        <div class="col-span-12 md:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                วิธีเดินทาง <span class="text-rose-400">*</span>
                            </label>
                            <input
                                type="text"
                                :name="`travel_infos[${index}][travel_type]`"
                                x-model="row.travel_type"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น BTS, รถยนต์, รถเมล์"
                            >
                        </div>

                        {{-- Start Place --}}
                        <div class="col-span-12 md:col-span-4">
                            <label class="mb-1 block text-xs font-medium text-slate-400">จุดเริ่มต้น</label>
                            <input
                                type="text"
                                :name="`travel_infos[${index}][start_place]`"
                                x-model="row.start_place"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น สถานีสนามไชย, อนุสาวรีย์ชัยฯ"
                            >
                        </div>

                        {{-- Distance --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ระยะทาง (กม.)</label>
                            <input
                                type="number"
                                :name="`travel_infos[${index}][distance_km]`"
                                x-model="row.distance_km"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                step="0.1"
                                min="0"
                            >
                        </div>

                        {{-- Duration --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">เวลา (นาที)</label>
                            <input
                                type="number"
                                :name="`travel_infos[${index}][duration_minutes]`"
                                x-model="row.duration_minutes"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                min="0"
                            >
                        </div>

                        {{-- เปิดใช้งาน --}}
                        <div class="col-span-12 flex items-center justify-between gap-3 md:col-span-1 md:flex-col md:items-end">
                            <label class="mt-6 flex cursor-pointer items-center gap-1.5 text-xs text-slate-400 md:mt-7">
                                <input
                                    type="checkbox"
                                    :name="`travel_infos[${index}][is_active]`"
                                    value="1"
                                    x-model="row.is_active"
                                    class="h-3.5 w-3.5 rounded border-white/20 bg-slate-950 text-blue-600"
                                >
                                ใช้งาน
                            </label>
                        </div>

                        {{-- Cost Estimate --}}
                        <div class="col-span-12 md:col-span-4">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ใช้จ่ายโดยประมาณ</label>
                            <input
                                type="text"
                                :name="`travel_infos[${index}][cost_estimate]`"
                                x-model="row.cost_estimate"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น 30-50 บาท"
                            >
                        </div>

                        {{-- Note --}}
                        <div class="col-span-12 md:col-span-8">
                            <label class="mb-1 block text-xs font-medium text-slate-400">หมายเหตุ</label>
                            <input
                                type="text"
                                :name="`travel_infos[${index}][note]`"
                                x-model="row.note"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น เดินต่อประมาณ 5 นาทีจากสถานี"
                            >
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

            <p x-show="rows.length === 0" class="rounded-2xl border border-dashed border-white/10 bg-slate-950/30 px-4 py-5 text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มข้อมูลเดินทางเพื่อเพิ่มวิธีเดินทาง
            </p>
        </div>
    </section>
