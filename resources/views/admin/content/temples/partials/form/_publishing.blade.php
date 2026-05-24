    @php
        $canPublishTemple = auth('admin')->user()?->hasPermission('temples.publish') ?? false;
        $currentStatus = old('status', $content?->status);
        $statusLabels = [
            'draft' => 'ฉบับร่าง',
            'review' => 'รอตรวจสอบ',
            'published' => 'เผยแพร่',
            'archived' => 'เก็บถาวร',
        ];
        $visibleStatusOptions = collect($statusOptions)
            ->when($canPublishTemple || $currentStatus === 'published', fn ($options) => $options->push('published'))
            ->unique()
            ->values();
    @endphp

    {{-- Section: Publishing --}}
    <section
        x-data="{
            status: @js($currentStatus ?: 'draft'),
            publishedAt: @js(old('published_at', $content?->published_at?->format('Y-m-d\TH:i'))),
            formatNow() {
                const now = new Date();
                now.setSeconds(0, 0);
                const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000);

                return local.toISOString().slice(0, 16);
            },
            useCurrentTime() {
                this.publishedAt = this.formatNow();
            },
            syncPublishTime() {
                if (this.status === 'published' && !this.publishedAt) {
                    this.useCurrentTime();
                }
            },
        }"
        x-init="syncPublishTime()"
        class="temple-panel temple-panel-publish overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
    >
        <div id="temple-publishing" class="border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">การเผยแพร่</h2>
            <p class="mt-1 text-xs text-slate-400">กำหนดสถานะ เวลาเผยแพร่ และการแสดงผลของวัด</p>
        </div>

        <div class="space-y-6 p-6">
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-300">
                        สถานะ <span class="text-rose-400">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        x-model="status"
                        @change="syncPublishTime()"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('status') border-rose-400 @enderror"
                    >
                        @foreach ($visibleStatusOptions as $opt)
                            <option value="{{ $opt }}" @selected($currentStatus === $opt)>
                                {{ $statusLabels[$opt] ?? $opt }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-3">
                        <label for="published_at" class="block text-sm font-medium text-slate-300">เผยแพร่เมื่อ</label>
                        <button
                            type="button"
                            @click="useCurrentTime()"
                            class="text-xs font-medium text-blue-300 hover:text-blue-200"
                        >
                            ใช้เวลาปัจจุบัน
                        </button>
                    </div>
                    <input
                        type="datetime-local"
                        id="published_at"
                        name="published_at"
                        x-model="publishedAt"
                        class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 @error('published_at') border-rose-400 @enderror"
                    >
                    @error('published_at')
                        <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4 text-slate-300 hover:bg-white/[0.06]">
                    <input
                        type="checkbox"
                        name="is_featured"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600"
                        @checked(old('is_featured', $content?->is_featured))
                    >
                    <div>
                        <div class="text-sm font-medium text-slate-200">แนะนำบนหน้าเว็บ</div>
                        <div class="text-xs text-slate-500">ใช้เน้นในส่วนสำคัญของเว็บไซต์</div>
                    </div>
                </label>

                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4 text-slate-300 hover:bg-white/[0.06]">
                    <input
                        type="checkbox"
                        name="is_popular"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-600"
                        @checked(old('is_popular', $content?->is_popular))
                    >
                    <div>
                        <div class="text-sm font-medium text-slate-200">ยอดนิยม</div>
                        <div class="text-xs text-slate-500">กำหนดให้วัดนี้อยู่ในกลุ่มยอดนิยม</div>
                    </div>
                </label>
            </div>
        </div>
    </section>
