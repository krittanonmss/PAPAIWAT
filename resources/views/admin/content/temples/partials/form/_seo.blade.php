        {{-- Section: SEO --}}
        <section class="temple-panel temple-panel-publish overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
            <div class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">SEO</h2>
                <p class="mt-1 text-xs text-slate-400">ข้อมูลสำหรับ title และ description ของหน้าเว็บ</p>
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-300">Meta Title</label>
                    <input
                        type="text"
                        id="meta_title"
                        name="meta_title"
                        value="{{ old('meta_title', $content?->meta_title) }}"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="เช่น พระแก้ว | PAPAIWAT"
                    >
                    <p class="mt-1 text-xs text-slate-500">ควรสั้น กระชับ และสื่อถึงชื่อหรือจังหวัด</p>
                </div>

                <div>
                    <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-300">Meta คำอธิบาย</label>
                    <textarea
                        id="meta_description"
                        name="meta_description"
                        rows="3"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                        placeholder="คำอธิบายสั้นสำหรับผลการค้นหา เช่น ประวัติ จุดเด่น และข้อมูลการเข้าชม"
                    >{{ old('meta_description', $content?->meta_description) }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">ใช้สำหรับแสดงในผลการค้นหาและ ตัวอย่างเวลาแชร์</p>
                </div>
            </div>
        </section>
