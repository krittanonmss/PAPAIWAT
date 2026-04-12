<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-6">
        <div>
            <label for="file" class="mb-2 block text-sm font-medium text-slate-700">
                ไฟล์
            </label>
            <input
                type="file"
                id="file"
                name="file"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:text-white hover:file:bg-slate-800"
                required
            >
            <p class="mt-1 text-xs text-slate-500">
                รองรับรูปภาพและไฟล์ทั่วไป ขนาดไม่เกิน 10 MB
            </p>
            @error('file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="media_folder_id" class="mb-2 block text-sm font-medium text-slate-700">
                โฟลเดอร์
            </label>
            <select
                id="media_folder_id"
                name="media_folder_id"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="">-- ไม่ระบุโฟลเดอร์ --</option>
                @foreach ($folders as $folder)
                    <option value="{{ $folder->id }}" @selected((string) old('media_folder_id') === (string) $folder->id)>
                        {{ $folder->name }}
                    </option>
                @endforeach
            </select>
            @error('media_folder_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="title" class="mb-2 block text-sm font-medium text-slate-700">
                ชื่อแสดงผล
            </label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                placeholder="เช่น รูปปกวัดพระแก้ว"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="alt_text" class="mb-2 block text-sm font-medium text-slate-700">
                Alt Text
            </label>
            <input
                type="text"
                id="alt_text"
                name="alt_text"
                value="{{ old('alt_text') }}"
                placeholder="คำอธิบายภาพสำหรับ accessibility"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            <p class="mt-1 text-xs text-slate-500">
                แนะนำให้กรอกเมื่ออัปโหลดรูปที่จะใช้บนหน้าเว็บไซต์
            </p>
            @error('alt_text')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="visibility" class="mb-2 block text-sm font-medium text-slate-700">
                Visibility
            </label>
            <select
                id="visibility"
                name="visibility"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="public" @selected(old('visibility', 'public') === 'public')>Public</option>
                <option value="private" @selected(old('visibility') === 'private')>Private</option>
            </select>
            @error('visibility')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="space-y-6">
        <div>
            <label for="caption" class="mb-2 block text-sm font-medium text-slate-700">
                Caption
            </label>
            <textarea
                id="caption"
                name="caption"
                rows="4"
                placeholder="คำบรรยายสั้นของไฟล์"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >{{ old('caption') }}</textarea>
            @error('caption')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="mb-2 block text-sm font-medium text-slate-700">
                Description
            </label>
            <textarea
                id="description"
                name="description"
                rows="6"
                placeholder="รายละเอียดเพิ่มเติมของไฟล์"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>