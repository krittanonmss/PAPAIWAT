    {{-- Section: Temple Details --}}
    <section class="temple-panel temple-panel-details overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div id="temple-details" class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ข้อมูลเฉพาะของวัด</h2>
            <p class="mt-1 text-xs text-slate-400">ประเภท นิกาย สถาปัตยกรรม ประวัติ และข้อแนะนำการเข้าชม</p>
        </div>

        <div class="space-y-5 p-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="temple_type" class="mb-1.5 block text-sm font-medium text-slate-300">ประเภท</label>
                    <select
                        id="temple_type"
                        name="temple_type"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกประเภท —</option>
                        @foreach (['ราษฎร์', 'พระอารามหลวง', 'หลวง', 'สำนักสงฆ์', 'ร้าง', 'อื่น ๆ'] as $option)
                            <option value="{{ $option }}" @selected(old('temple_type', $temple?->temple_type) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sect" class="mb-1.5 block text-sm font-medium text-slate-300">นิกาย</label>
                    <select
                        id="sect"
                        name="sect"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกนิกาย —</option>
                        @foreach (['มหานิกาย', 'ธรรมยุติกนิกาย', 'จีนนิกาย', 'อนัมนิกาย', 'ไม่ระบุ', 'อื่น ๆ'] as $option)
                            <option value="{{ $option }}" @selected(old('sect', $temple?->sect) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="architecture_style" class="mb-1.5 block text-sm font-medium text-slate-300">รูปแบบสถาปัตยกรรม</label>
                    <select
                        id="architecture_style"
                        name="architecture_style"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกรูปแบบสถาปัตยกรรม —</option>
                        @foreach (['รัตนโกสินทร์', 'อยุธยา', 'สุโขทัย', 'ล้านนา', 'ล้านช้าง', 'ขอม', 'จีน', 'ไทยร่วมสมัย', 'ผสมผสาน', 'อื่น ๆ'] as $option)
                            <option value="{{ $option }}" @selected(old('architecture_style', $temple?->architecture_style) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="founded_year" class="mb-1.5 block text-sm font-medium text-slate-300">ปีที่ก่อตั้ง</label>
                    <input
                        type="number"
                        id="founded_year"
                        name="founded_year"
                        value="{{ old('founded_year', $temple?->founded_year) }}"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="เช่น 1782"
                        min="0"
                    >
                </div>

                <div>
                    <label for="dress_code" class="mb-1.5 block text-sm font-medium text-slate-300">การแต่งกาย</label>
                    <select
                        id="dress_code"
                        name="dress_code"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">— เลือกคำแนะนำการแต่งกาย —</option>
                        @foreach ([
                            'แต่งกายสุภาพ',
                            'แต่งกายสุภาพ ห้ามเสื้อแขนกุดและกางเกงขาสั้น',
                            'แต่งกายสุภาพ ถอดรองเท้าก่อนเข้าอาคาร',
                            'แต่งกายสุภาพและงดใช้เสียงดัง',
                            'ไม่มีข้อกำหนดเฉพาะ',
                            'อื่น ๆ',
                        ] as $option)
                            <option value="{{ $option }}" @selected(old('dress_code', $temple?->dress_code) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="recommended_visit_start_time" class="mb-1.5 block text-sm font-medium text-slate-300">
                            เวลาแนะนำให้ไป (เริ่ม)
                        </label>
                        <input
                            type="time"
                            id="recommended_visit_start_time"
                            name="recommended_visit_start_time"
                            value="{{ old('recommended_visit_start_time', $temple?->recommended_visit_start_time ? substr((string) $temple->recommended_visit_start_time, 0, 5) : '') }}"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>

                    <div>
                        <label for="recommended_visit_end_time" class="mb-1.5 block text-sm font-medium text-slate-300">
                            เวลาแนะนำให้ไป (สิ้นสุด)
                        </label>
                        <input
                            type="time"
                            id="recommended_visit_end_time"
                            name="recommended_visit_end_time"
                            value="{{ old('recommended_visit_end_time', $temple?->recommended_visit_end_time ? substr((string) $temple->recommended_visit_end_time, 0, 5) : '') }}"
                            class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        >
                    </div>
                </div>
            </div>

            @include('admin.content.temples.partials._rich_text_editor', [
                'name' => 'history',
                'id' => 'history',
                'label' => 'ประวัติ',
                'value' => $temple?->history,
                'placeholder' => 'ประวัติความเป็นมา เหตุการณ์สำคัญ บุคคลสำคัญ หรือข้อมูลเชิงวัฒนธรรมของวัด',
                'hint' => 'เหมาะกับเนื้อหายาว แยกย่อหน้าและหัวข้อได้',
                'minHeight' => '300px',
            ])
        </div>
    </section>
