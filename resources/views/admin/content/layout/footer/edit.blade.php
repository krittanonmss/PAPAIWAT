<x-layouts.admin :title="'Footer Settings'" header="ตั้ง Footer">
    <div class="space-y-6 text-white">
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 shadow-xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300">
                        Footer Customization
                    </div>

                    <h1 class="text-2xl font-bold text-white">ตั้ง Footer</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        ปรับข้อความ แบรนด์ รูปแบบพื้นหลัง และการแสดงผล footer ส่วนคอลัมน์ลิงก์จัดการผ่าน Footer Menu
                    </p>
                </div>

                <a
                    href="{{ route('admin.content.menus.index') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white"
                >
                    ไปจัดการเมนู
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-lg shadow-emerald-950/20">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-4 text-sm text-rose-200 shadow-lg shadow-rose-950/20">
                <p class="font-semibold text-rose-100">กรุณาตรวจสอบข้อมูลที่กรอก</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.content.footer.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
                <div class="space-y-6">
                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 pb-4">
                            <p class="text-sm font-medium text-blue-300">Brand</p>
                            <h2 class="mt-1 text-lg font-semibold text-white">ข้อความฝั่งซ้ายของ Footer</h2>
                        </div>

                        <div class="mt-5 space-y-5">
                            <div>
                                <label for="brand_title" class="mb-1.5 block text-sm font-medium text-slate-300">ชื่อแบรนด์</label>
                                <input
                                    id="brand_title"
                                    name="brand_title"
                                    value="{{ old('brand_title', $settings['brand_title']) }}"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                    placeholder="เช่น PAPAIWAT"
                                >
                            </div>

                            <div>
                                <label for="brand_description" class="mb-1.5 block text-sm font-medium text-slate-300">คำอธิบายแบรนด์</label>
                                <textarea
                                    id="brand_description"
                                    name="brand_description"
                                    rows="4"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >{{ old('brand_description', $settings['brand_description']) }}</textarea>
                            </div>

                            <div>
                                <label for="footer_note" class="mb-1.5 block text-sm font-medium text-slate-300">ข้อความเสริม</label>
                                <textarea
                                    id="footer_note"
                                    name="footer_note"
                                    rows="3"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                    placeholder="เช่น เวลาทำการ หรือข้อความประชาสัมพันธ์สั้นๆ"
                                >{{ old('footer_note', $settings['footer_note']) }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 pb-4">
                            <p class="text-sm font-medium text-blue-300">Display</p>
                            <h2 class="mt-1 text-lg font-semibold text-white">รูปแบบการแสดงผล</h2>
                        </div>

                        <div class="mt-5 grid gap-5 lg:grid-cols-2">
                            <div>
                                <label for="background_style" class="mb-1.5 block text-sm font-medium text-slate-300">พื้นหลัง</label>
                                <select
                                    id="background_style"
                                    name="background_style"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option value="glass" class="bg-slate-900" @selected(old('background_style', $settings['background_style']) === 'glass')>โปร่งแสง</option>
                                    <option value="solid" class="bg-slate-900" @selected(old('background_style', $settings['background_style']) === 'solid')>เข้มทึบ</option>
                                    <option value="minimal" class="bg-slate-900" @selected(old('background_style', $settings['background_style']) === 'minimal')>เรียบ ไม่มีพื้นหลัง</option>
                                </select>
                            </div>

                            <div>
                                <label for="column_count" class="mb-1.5 block text-sm font-medium text-slate-300">จำนวนคอลัมน์บน desktop</label>
                                <select
                                    id="column_count"
                                    name="column_count"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option value="3" class="bg-slate-900" @selected(old('column_count', $settings['column_count']) === '3')>3 คอลัมน์</option>
                                    <option value="4" class="bg-slate-900" @selected(old('column_count', $settings['column_count']) === '4')>4 คอลัมน์</option>
                                    <option value="5" class="bg-slate-900" @selected(old('column_count', $settings['column_count']) === '5')>5 คอลัมน์</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            @foreach ([
                                'show_brand' => 'แสดงส่วนแบรนด์',
                                'show_menu' => 'แสดงเมนู Footer',
                                'show_bottom_bar' => 'แสดงแถบลิขสิทธิ์ด้านล่าง',
                                'show_border' => 'แสดงเส้นคั่นด้านบน',
                            ] as $key => $label)
                                <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3">
                                    <input
                                        type="checkbox"
                                        name="{{ $key }}"
                                        value="1"
                                        class="h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600 focus:ring-blue-500"
                                        @checked(old($key, $settings[$key]))
                                    >
                                    <span class="text-sm font-medium text-slate-200">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <div class="border-b border-white/10 pb-4">
                            <p class="text-sm font-medium text-blue-300">Bottom Bar</p>
                            <h2 class="mt-1 text-lg font-semibold text-white">ข้อความลิขสิทธิ์</h2>
                        </div>

                        <div class="mt-5">
                            <label for="copyright_text" class="mb-1.5 block text-sm font-medium text-slate-300">Copyright</label>
                            <input
                                id="copyright_text"
                                name="copyright_text"
                                value="{{ old('copyright_text', $settings['copyright_text']) }}"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                                placeholder="© {year} {brand}. All rights reserved."
                            >
                            <p class="mt-1 text-xs text-slate-500">ใช้ตัวแปรได้: {year}, {brand}</p>
                        </div>
                    </section>
                </div>

                <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
                    <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-5 shadow-xl shadow-slate-950/30 backdrop-blur">
                        <h3 class="text-sm font-semibold text-white">วิธีใช้งาน</h3>
                        <div class="mt-4 space-y-3 text-xs leading-5 text-slate-400">
                            <p>1. ปรับข้อความและรูปแบบ Footer จากหน้านี้</p>
                            <p>2. สร้าง Menu ที่ตำแหน่งเป็น Footer เพื่อจัดคอลัมน์ลิงก์</p>
                            <p>3. ใช้ Menu Item ประเภทหัวข้อกลุ่มสำหรับชื่อคอลัมน์ และสร้าง child เป็นลิงก์จริง</p>
                        </div>
                    </section>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/40 transition hover:opacity-90"
                    >
                        บันทึก Footer
                    </button>
                </aside>
            </div>
        </form>
    </div>
</x-layouts.admin>
