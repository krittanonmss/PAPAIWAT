    {{-- Section: Nearby Places --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="nearbyPlacesManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">ใกล้เคียง</h2>
                <p class="mt-1 text-xs text-slate-400">เชื่อมโยงที่อยู่ใกล้กันหรือเกี่ยวข้องกัน</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มใกล้เคียง
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">
                        {{-- Temple --}}
                        <div class="col-span-12 md:col-span-5">
                            <label class="mb-1 block text-xs font-medium text-slate-400">
                                ที่เกี่ยวข้อง <span class="text-rose-400">*</span>
                            </label>

                            <input
                                type="search"
                                x-model.debounce.100ms="row.temple_search"
                                @input.debounce.250ms="searchNearbyTemples(row)"
                                class="mb-2 w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="ค้นหาชื่อหรือ ID..."
                            >
                            <select
                                :name="`nearby_places[${index}][nearby_temple_id]`"
                                x-model="row.nearby_temple_id"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                            >
                                <option value="">— เลือก —</option>
                                <template x-for="temple in filteredNearbyTemples(row)" :key="temple.id">
                                    <option :value="temple.id" x-text="temple.title"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Relation Type --}}
                        <div class="col-span-12 md:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ประเภทความเกี่ยวข้อง</label>

                            <input
                                type="text"
                                :name="`nearby_places[${index}][relation_type]`"
                                x-model="row.relation_type"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น nearby, same_complex"
                            >
                        </div>

                        {{-- Distance --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ระยะทาง (กม.)</label>

                            <input
                                type="number"
                                :name="`nearby_places[${index}][distance_km]`"
                                x-model="row.distance_km"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                step="0.1"
                                min="0"
                            >
                        </div>

                        {{-- Duration --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">เวลาเดินทาง (นาที)</label>

                            <input
                                type="number"
                                :name="`nearby_places[${index}][duration_minutes]`"
                                x-model="row.duration_minutes"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                min="0"
                            >
                        </div>

                        {{-- Score --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">คะแนนความเกี่ยวข้อง</label>

                            <input
                                type="number"
                                :name="`nearby_places[${index}][score]`"
                                x-model="row.score"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                step="0.1"
                            >
                        </div>

                        {{-- ลำดับ --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ลำดับ</label>

                            <input
                                type="number"
                                :name="`nearby_places[${index}][sort_order]`"
                                x-model="row.sort_order"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="0"
                                min="0"
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
                ยังไม่มีข้อมูล — กดเพิ่มใกล้เคียงเพื่อเชื่อมโยงที่เกี่ยวข้อง
            </p>
        </div>
    </section>
