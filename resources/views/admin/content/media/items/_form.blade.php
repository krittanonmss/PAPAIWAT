<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-6">
        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">ไฟล์สื่อ</h2>
            <p class="mt-1 text-sm text-slate-400">เลือกไฟล์และโฟลเดอร์สำหรับจัดเก็บ</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="file" class="mb-2 block text-sm font-medium text-slate-300">
                        ไฟล์ <span class="text-red-400">*</span>
                    </label>

                    <input
                        type="file"
                        id="file"
                        name="files[]"
                        multiple
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2 text-sm text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-600 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-500"
                        required
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        เลือกได้หลายไฟล์ รองรับรูปภาพและไฟล์ทั่วไป ขนาดไม่เกิน 5 MB ต่อไฟล์
                    </p>

                    @error('file')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    @error('files')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    @error('files.*')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="media_folder_id" class="mb-2 block text-sm font-medium text-slate-300">
                        โฟลเดอร์
                    </label>

                    <select
                        id="media_folder_id"
                        name="media_folder_id"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="">-- ไม่ระบุโฟลเดอร์ --</option>
                        @foreach ($folders as $folder)
                            <option value="{{ $folder->id }}" @selected((string) old('media_folder_id') === (string) $folder->id)>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('media_folder_id')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">ข้อมูลแสดงผล</h2>
            <p class="mt-1 text-sm text-slate-400">ข้อมูลที่ใช้แสดงในระบบและหน้าเว็บไซต์</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="title" class="mb-2 block text-sm font-medium text-slate-300">
                        ชื่อแสดงผล
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="เช่น รูปปกวัดพระแก้ว"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >

                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alt_text" class="mb-2 block text-sm font-medium text-slate-300">
                        Alt Text
                    </label>

                    <input
                        type="text"
                        id="alt_text"
                        name="alt_text"
                        value="{{ old('alt_text') }}"
                        placeholder="คำอธิบายภาพสำหรับ accessibility"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        แนะนำให้กรอกเมื่ออัปโหลดรูปที่จะใช้บนหน้าเว็บไซต์
                    </p>

                    @error('alt_text')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="visibility" class="mb-2 block text-sm font-medium text-slate-300">
                        Visibility
                    </label>

                    <select
                        id="visibility"
                        name="visibility"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="public" @selected(old('visibility', 'public') === 'public')>Public</option>
                        <option value="private" @selected(old('visibility') === 'private')>Private</option>
                    </select>

                    @error('visibility')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
            <h2 class="text-base font-semibold text-white">รายละเอียดเพิ่มเติม</h2>
            <p class="mt-1 text-sm text-slate-400">คำบรรยายและรายละเอียดของไฟล์</p>

            <div class="mt-5 space-y-5">
                <div>
                    <label for="caption" class="mb-2 block text-sm font-medium text-slate-300">
                        Caption
                    </label>

                    <textarea
                        id="caption"
                        name="caption"
                        rows="4"
                        placeholder="คำบรรยายสั้นของไฟล์"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >{{ old('caption') }}</textarea>

                    @error('caption')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-slate-300">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="8"
                        placeholder="รายละเอียดเพิ่มเติมของไฟล์"
                        class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20"
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
