    {{-- Section: Opening Hours --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="openingHoursManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">เวลาเปิด-ปิด</h2>
                <p class="mt-1 text-xs text-slate-400">กำหนดเวลาทำการแบบช่วงวัน เช่น ทุกวัน หรือ จันทร์ - ศุกร์</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มช่วงเวลา
            </button>
        </div>

        <div class="space-y-4 p-6">
            <template x-for="(row, rowIndex) in rows" :key="rowIndex">
                <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                    <div class="grid grid-cols-12 items-start gap-3">

                        {{-- Preset --}}
                        <div class="col-span-12 md:col-span-3">
                            <label class="mb-1 block text-xs font-medium text-slate-400">รูปแบบวัน</label>
                            <select
                                x-model="row.preset"
                                @change="applyPreset(row)"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400"
                            >
                                <option value="everyday">ทุกวัน</option>
                                <option value="weekdays">จันทร์ - ศุกร์</option>
                                <option value="weekend">เสาร์ - อาทิตย์</option>
                                <option value="oneday">วันเดียว</option>
                                <option value="custom">กำหนดเอง</option>
                            </select>
                        </div>

                        {{-- From Day --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">จากวัน</label>
                            <select
                                x-model.number="row.day_from"
                                :disabled="!['custom','oneday'].includes(row.preset)"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400 disabled:opacity-50"
                            >
                                @foreach ($days as $di => $dayName)
                                    <option value="{{ $di }}">{{ $dayName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- To Day --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ถึงวัน</label>
                            <select
                                x-model.number="row.day_to"
                                :disabled="!['custom','oneday'].includes(row.preset)"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400 disabled:opacity-50"
                            >
                                @foreach ($days as $di => $dayName)
                                    <option value="{{ $di }}">{{ $dayName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Open --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">เปิด</label>
                            <input
                                type="time"
                                x-model="row.open_time"
                                :disabled="row.is_closed"
                                class="w-full min-w-[120px] rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400 disabled:opacity-50"
                            >
                        </div>

                        {{-- Close --}}
                        <div class="col-span-6 md:col-span-2">
                            <label class="mb-1 block text-xs font-medium text-slate-400">ปิด</label>
                            <input
                                type="time"
                                x-model="row.close_time"
                                :disabled="row.is_closed"
                                class="w-full min-w-[120px] rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none focus:border-blue-400 disabled:opacity-50"
                            >
                        </div>

                        {{-- การจัดการs --}}
                        <div class="col-span-12 flex items-center justify-between gap-3 md:col-span-1 md:flex-col md:items-end">
                            <label class="flex cursor-pointer items-center gap-1.5 text-xs text-slate-400">
                                <input
                                    type="checkbox"
                                    x-model="row.is_closed"
                                    class="h-3.5 w-3.5 rounded border-white/20 bg-slate-950 text-blue-600"
                                >
                                ปิด
                            </label>

                            <button
                                type="button"
                                @click="removeRow(rowIndex)"
                                class="rounded-lg border border-rose-400/30 bg-rose-500/10 px-2 py-1.5 text-xs text-rose-300 hover:bg-rose-500/20"
                            >
                                ✕
                            </button>
                        </div>

                        {{-- Note --}}
                        <div class="col-span-12">
                            <label class="mb-1 block text-xs font-medium text-slate-400">หมายเหตุ</label>
                            <input
                                type="text"
                                x-model="row.note"
                                class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                placeholder="เช่น วันหยุดนักขัตฤกษ์"
                            >
                        </div>
                    </div>

                    <div class="mt-3 rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2 text-xs text-slate-400">
                        จะบันทึกเป็น:
                        <span class="text-slate-200" x-text="previewDays(row)"></span>
                    </div>
                </div>
            </template>

            <p x-show="rows.length === 0" class="text-sm text-slate-400">
                ยังไม่มีข้อมูล — กดเพิ่มช่วงเวลาเพื่อเพิ่ม
            </p>

            <template x-for="(item, index) in expandedRows()" :key="index">
                <div>
                    <input type="hidden" :name="`opening_hours[${index}][day_of_week]`" :value="item.day_of_week">
                    <input type="hidden" :name="`opening_hours[${index}][open_time]`" :value="item.is_closed ? '' : item.open_time">
                    <input type="hidden" :name="`opening_hours[${index}][close_time]`" :value="item.is_closed ? '' : item.close_time">
                    <input type="hidden" :name="`opening_hours[${index}][note]`" :value="item.note">
                    <input type="hidden" :name="`opening_hours[${index}][is_closed]`" :value="item.is_closed ? 1 : 0">
                </div>
            </template>
        </div>
    </section>
