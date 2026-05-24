    {{-- Section: Address --}}
    <section class="temple-panel temple-panel-media overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">ที่ตั้งและแผนที่</h2>
            <p class="mt-1 text-xs text-slate-400">ที่อยู่สำหรับแสดงผลและข้อมูลพิกัดสำหรับลิงก์แผนที่</p>
        </div>
        <div class="grid gap-5 p-6 2xl:grid-cols-[minmax(0,1fr)_420px]">
            <div class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-200">ที่อยู่</h3>
                    <p class="mt-1 text-xs text-slate-500">ข้อมูลนี้ใช้แสดงในหน้า Detail และตัวกรองจังหวัด</p>
                </div>

                <div>
                    <label for="address_line" class="mb-1.5 block text-sm font-medium text-slate-300">ที่อยู่</label>
                    <input type="text" id="address_line" name="address[address_line]" value="{{ old('address.address_line', $address?->address_line) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="เลขที่ ถนน หรือชื่อชุมชน">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="subdistrict" class="mb-1.5 block text-sm font-medium text-slate-300">แขวง / ตำบล</label>
                        <input type="text" id="subdistrict" name="address[subdistrict]" value="{{ old('address.subdistrict', $address?->subdistrict) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label for="district" class="mb-1.5 block text-sm font-medium text-slate-300">เขต / อำเภอ</label>
                        <input type="text" id="district" name="address[district]" value="{{ old('address.district', $address?->district) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label for="province" class="mb-1.5 block text-sm font-medium text-slate-300">จังหวัด</label>
                        <input type="text" id="province" name="address[province]" value="{{ old('address.province', $address?->province) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label for="postal_code" class="mb-1.5 block text-sm font-medium text-slate-300">รหัสไปรษณีย์</label>
                        <input type="text" id="postal_code" name="address[postal_code]" value="{{ old('address.postal_code', $address?->postal_code) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>
            </div>

            <aside class="space-y-5 rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-200">พิกัดและแผนที่</h3>
                    <p class="mt-1 text-xs text-slate-500">กรอกพิกัดเมื่อต้องการแสดงตำแหน่งแบบแม่นยำ</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-1">
                    <div>
                        <label for="latitude" class="mb-1.5 block text-sm font-medium text-slate-300">ละติจูด</label>
                        <input type="text" id="latitude" name="address[latitude]" value="{{ old('address.latitude', $address?->latitude) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 font-mono text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="13.7563">
                    </div>
                    <div>
                        <label for="longitude" class="mb-1.5 block text-sm font-medium text-slate-300">ลองจิจูด</label>
                        <input type="text" id="longitude" name="address[longitude]" value="{{ old('address.longitude', $address?->longitude) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 font-mono text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="100.5018">
                    </div>
                </div>

                <div>
                    <label for="google_place_id" class="mb-1.5 block text-sm font-medium text-slate-300">Google Place ID</label>
                    <input type="text" id="google_place_id" name="address[google_place_id]" value="{{ old('address.google_place_id', $address?->google_place_id) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 font-mono text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label for="google_maps_url" class="mb-1.5 block text-sm font-medium text-slate-300">Google Maps URL</label>
                    <input type="url" id="google_maps_url" name="address[google_maps_url]" value="{{ old('address.google_maps_url', $address?->google_maps_url) }}" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20" placeholder="https://maps.google.com/...">
                </div>
            </aside>
        </div>
    </section>
