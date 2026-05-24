        @php
            $canPublishArticle = auth('admin')->user()?->hasPermission('articles.publish') ?? false;
            $currentStatus = old('status', $content->status ?? 'draft');
            $statusLabels = [
                'draft' => 'ฉบับร่าง',
                'review' => 'รอตรวจสอบ',
                'published' => 'เผยแพร่',
                'archived' => 'เก็บถาวร',
            ];
            $visibleStatusOptions = collect(['draft', 'review', 'archived'])
                ->when($canPublishArticle || $currentStatus === 'published', fn ($options) => $options->push('published'))
                ->unique()
                ->values();
        @endphp

        {{-- Publishing --}}
        <section
            x-data="{
                status: @js($currentStatus ?: 'draft'),
                publishedAt: @js(old('published_at', isset($content?->published_at) ? $content->published_at->format('Y-m-d\TH:i') : '')),
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
            class="article-panel article-panel-publish overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] shadow-xl shadow-slate-950/30 backdrop-blur"
        >
            <div id="article-publishing" class="border-b border-white/10 px-6 py-4">
                <h2 class="text-base font-semibold text-white">การเผยแพร่</h2>
                <p class="mt-1 text-xs text-slate-400">กำหนดสถานะ เวลาเผยแพร่ และการแสดงผลของบทความ</p>
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
                            class="@error('status') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        >
                            @foreach ($visibleStatusOptions as $opt)
                                <option value="{{ $opt }}" @selected($currentStatus === $opt)>
                                    {{ $statusLabels[$opt] ?? $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <label for="published_at" class="block text-sm font-medium text-slate-300">
                                วันที่เผยแพร่
                            </label>
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
                            class="@error('published_at') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('published_at')
                            <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="scheduled_at" class="mb-1.5 block text-sm font-medium text-slate-300">
                            ตั้งเวลาเริ่มแสดง
                        </label>
                        <input
                            type="datetime-local"
                            id="scheduled_at"
                            name="scheduled_at"
                            value="{{ old('scheduled_at', isset($article?->scheduled_at) ? $article->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                            class="@error('scheduled_at') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('scheduled_at')
                            <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expired_at" class="mb-1.5 block text-sm font-medium text-slate-300">
                            ตั้งเวลาหมดอายุ
                        </label>
                        <input
                            type="datetime-local"
                            id="expired_at"
                            name="expired_at"
                            value="{{ old('expired_at', isset($article?->expired_at) ? $article->expired_at->format('Y-m-d\TH:i') : '') }}"
                            class="@error('expired_at') border-rose-400/60 @else border-white/10 @enderror w-full rounded-xl border bg-slate-950/50 px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-400/60 focus:ring-2 focus:ring-blue-500/20"
                        >
                        @error('expired_at')
                            <p class="mt-1.5 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input type="hidden" name="is_featured" value="0">
                        <input
                            type="checkbox"
                            name="is_featured"
                            value="1"
                            @checked(old('is_featured', $content->is_featured ?? false))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">แนะนำ</div>
                            <div class="text-xs text-slate-500">ใช้เน้นในส่วนสำคัญของเว็บไซต์</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input type="hidden" name="is_popular" value="0">
                        <input
                            type="checkbox"
                            name="is_popular"
                            value="1"
                            @checked(old('is_popular', $content->is_popular ?? false))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">ยอดนิยม</div>
                            <div class="text-xs text-slate-500">กำหนดให้บทความนี้อยู่ในกลุ่มยอดนิยม</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input type="hidden" name="allow_comments" value="0">
                        <input
                            type="checkbox"
                            name="allow_comments"
                            value="1"
                            @checked(old('allow_comments', $article->allow_comments ?? true))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">เปิดความคิดเห็น</div>
                            <div class="text-xs text-slate-500">อนุญาตให้ผู้ใช้แสดงความคิดเห็นในนี้</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/40 p-4">
                        <input type="hidden" name="show_on_homepage" value="0">
                        <input
                            type="checkbox"
                            name="show_on_homepage"
                            value="1"
                            @checked(old('show_on_homepage', $article->show_on_homepage ?? false))
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950 text-blue-500 focus:ring-blue-500/30"
                        >
                        <div>
                            <div class="text-sm font-medium text-slate-200">แสดงบนหน้าแรก</div>
                            <div class="text-xs text-slate-500">นำนี้ไปแสดงใน section ของหน้าแรก</div>
                        </div>
                    </label>
                </div>
            </div>
        </section>
