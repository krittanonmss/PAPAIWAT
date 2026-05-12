<x-layouts.admin title="Comment Moderation" header="ความคิดเห็น">
    @php
        $statusStyles = [
            'pending' => 'border-yellow-400/20 bg-yellow-500/10 text-yellow-300',
            'approved' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300',
            'rejected' => 'border-red-400/20 bg-red-500/10 text-red-300',
            'spam' => 'border-red-400/20 bg-red-500/10 text-red-300',
        ];
    @endphp

    <div class="space-y-6 text-white">
        <section class="p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-300">Moderation</p>
                    <h1 class="mt-1 text-2xl font-bold">ความคิดเห็น</h1>
                    <p class="mt-2 text-sm text-slate-400">ตรวจสอบความคิดเห็นจากหน้า article และ temple ก่อนเผยแพร่</p>
                </div>
                <form method="GET" action="{{ route('admin.interactions.comments.index') }}" class="flex gap-2">
                    <select name="status" class="rounded-xl border px-3 py-2 text-sm">
                        <option value="">ทุกสถานะ</option>
                        @foreach (['pending' => 'รอตรวจ', 'approved' => 'อนุมัติ', 'rejected' => 'ปฏิเสธ'] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">กรอง</button>
                </form>
            </div>
        </section>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">{{ session('success') }}</div>
        @endif

        <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">เนื้อหา</th>
                            <th class="px-5 py-3 text-left font-medium">ความคิดเห็น</th>
                            <th class="px-5 py-3 text-left font-medium">สถานะ</th>
                            <th class="px-5 py-3 text-right font-medium">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($comments as $comment)
                            @php
                                $target = $comment->commentable;
                                $targetTitle = $target?->content?->title ?? '-';
                            @endphp
                            <tr>
                                <td class="px-5 py-4 align-top">
                                    <p class="font-semibold text-white">{{ $targetTitle }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ class_basename($comment->commentable_type) }} · {{ $comment->created_at?->format('Y-m-d H:i') }}</p>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <p class="text-slate-300">{{ $comment->body }}</p>
                                    <p class="mt-2 text-xs text-slate-500">โดย {{ $comment->display_name ?: 'ผู้เยี่ยมชม' }} · report {{ number_format($comment->report_count) }}</p>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs {{ $statusStyles[$comment->status] ?? 'border-white/10 bg-white/5 text-slate-300' }}">
                                        {{ $comment->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.interactions.comments.approve', $comment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">อนุมัติ</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.interactions.comments.reject', $comment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-xl bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-500">ปฏิเสธ</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.interactions.comments.destroy', $comment) }}" onsubmit="return confirm('ลบความคิดเห็นนี้หรือไม่?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-white/10">ลบ</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-slate-500">ยังไม่มีความคิดเห็น</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $comments->links() }}
    </div>
</x-layouts.admin>
