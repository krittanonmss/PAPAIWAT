    {{-- Section: Fees --}}
    <section
        class="temple-panel temple-panel-visit overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        x-data="feesManager()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-white">ธรรมเนียม</h2>
                <p class="mt-1 text-xs text-slate-400">เข้าชม จอดรถ หรือใช้จ่ายอื่น ๆ</p>
            </div>

            <button
                type="button"
                @click="addRow()"
                class="rounded-xl border border-blue-400/30 bg-blue-500/10 px-3 py-2 text-xs font-medium text-blue-300 transition hover:bg-blue-500/20"
            >
                + เพิ่มธรรมเนียม
            </button>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <div class="grid grid-cols-12 items-start gap-3">
                            {{-- Fee Type --}}
                            <div class="col-span-12 md:col-span-3">
                                <label class="mb-1 block text-xs font-medium text-slate-400">ประเภทธรรมเนียม</label>
                                <input
                                    type="text"
                                    :name="`fees[${index}][fee_type]`"
                                    x-model="row.fee_type"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="เช่น admission, parking"
                                >
                            </div>

                            {{-- Label --}}
                            <div class="col-span-12 md:col-span-4">
                                <label class="mb-1 block text-xs font-medium text-slate-400">ชื่อที่แสดง</label>
                                <input
                                    type="text"
                                    :name="`fees[${index}][label]`"
                                    x-model="row.label"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="เช่น เข้าชมผู้ใหญ่"
                                >
                            </div>

                            {{-- Amount --}}
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-xs font-medium text-slate-400">จำนวนเงิน</label>
                                <input
                                    type="number"
                                    :name="`fees[${index}][amount]`"
                                    x-model="row.amount"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="0"
                                    min="0"
                                    step="0.01"
                                >
                            </div>

                            {{-- Currency --}}
                            <div class="col-span-6 md:col-span-2">
                                <label class="mb-1 block text-xs font-medium text-slate-400">สกุลเงิน</label>
                                <input
                                    type="text"
                                    :name="`fees[${index}][currency]`"
                                    x-model="row.currency"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="THB"
                                >
                            </div>

                            {{-- เปิดใช้งาน --}}
                            <div class="col-span-12 flex items-center justify-between gap-3 md:col-span-1 md:flex-col md:items-end">
                                <label class="mt-6 flex cursor-pointer items-center gap-1.5 text-xs text-slate-400 md:mt-7">
                                    <input
                                        type="checkbox"
                                        :name="`fees[${index}][is_active]`"
                                        value="1"
                                        x-model="row.is_active"
                                        class="h-3.5 w-3.5 rounded border-white/20 bg-slate-950 text-blue-600"
                                    >
                                    ใช้งาน
                                </label>
                            </div>

                            {{-- Note --}}
                            <div class="col-span-12">
                                <label class="mb-1 block text-xs font-medium text-slate-400">หมายเหตุ</label>
                                <input
                                    type="text"
                                    :name="`fees[${index}][note]`"
                                    x-model="row.note"
                                    class="w-full rounded-lg border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-white outline-none placeholder:text-slate-500 focus:border-blue-400"
                                    placeholder="เช่น เด็กอายุต่ำกว่า 12 ปีเข้าฟรี"
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

                <p x-show="rows.length === 0" class="text-sm text-slate-400">
                    ยังไม่มีข้อมูล — กดเพิ่มธรรมเนียมเพื่อเพิ่ม
                </p>
            </div>
        </div>
    </section>
