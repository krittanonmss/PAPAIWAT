<?php

namespace App\Services\Admin\Content\Article;

use App\Models\Content\Article\Article;
use App\Models\Content\Category;
use App\Models\Content\Media\Media;
use Illuminate\Validation\ValidationException;

class ArticleValidationService
{
    public function validateForSave(array $validated, ?Article $article = null): void
    {
        $errors = [];

        $errors += $this->validateStatus($validated, $article);
        $errors += $this->validateCategories($validated);
        $errors += $this->validateCoverMedia($validated);
        $errors += $this->validateDates($validated);

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function validateForPublish(Article $article): void
    {
        $article->loadMissing('content.categories');

        $errors = [];

        if (! in_array($article->content?->status, ['draft', 'review'], true)) {
            $errors['status'] = 'เผยแพร่ได้เฉพาะบทความที่อยู่ในสถานะ draft หรือ review';
        }

        if (! $article->content?->title || ! $article->content?->slug) {
            $errors['title'] = 'ต้องมีชื่อและ slug ก่อนเผยแพร่';
        }

        if (! $article->body) {
            $errors['body'] = 'ต้องมีเนื้อหาบทความก่อนเผยแพร่';
        }

        if ($article->content?->categories->isEmpty()) {
            $errors['category_ids'] = 'ต้องเลือกหมวดหมู่อย่างน้อย 1 รายการก่อนเผยแพร่';
        }

        if ($article->scheduled_at && $article->scheduled_at->isFuture()) {
            $errors['scheduled_at'] = 'ยังไม่สามารถเผยแพร่ก่อนเวลาที่ตั้งเผยแพร่ได้';
        }

        if ($article->expired_at && $article->expired_at->lte(now())) {
            $errors['expired_at'] = 'ไม่สามารถเผยแพร่บทความที่หมดอายุแล้ว';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function validateForUnpublish(Article $article): void
    {
        if ($article->content?->status !== 'published') {
            throw ValidationException::withMessages([
                'status' => 'ยกเลิกเผยแพร่ได้เฉพาะบทความที่เผยแพร่แล้ว',
            ]);
        }
    }

    private function validateStatus(array $validated, ?Article $article): array
    {
        $status = $validated['status'] ?? 'draft';

        if ($status === 'published') {
            if ($article?->content?->status === 'published') {
                return [];
            }

            if (! auth('admin')->user()?->hasPermission('articles.publish')) {
                return ['status' => 'ต้องมีสิทธิ์เผยแพร่จึงจะตั้งสถานะเป็นเผยแพร่ได้'];
            }

            $errors = [];

            if (empty($validated['body'])) {
                $errors['body'] = 'ต้องมีเนื้อหาบทความก่อนเผยแพร่';
            }

            if (empty($validated['category_ids'])) {
                $errors['category_ids'] = 'ต้องเลือกหมวดหมู่อย่างน้อย 1 รายการก่อนเผยแพร่';
            }

            if (! empty($validated['scheduled_at']) && new \DateTimeImmutable($validated['scheduled_at']) > new \DateTimeImmutable('now')) {
                $errors['scheduled_at'] = 'ยังไม่สามารถเผยแพร่ก่อนเวลาที่ตั้งเผยแพร่ได้';
            }

            if (! empty($validated['expired_at']) && new \DateTimeImmutable($validated['expired_at']) <= new \DateTimeImmutable('now')) {
                $errors['expired_at'] = 'ไม่สามารถเผยแพร่บทความที่หมดอายุแล้ว';
            }

            return $errors;
        }

        if (! $article?->content || $article->content->status !== 'published') {
            return [];
        }

        if (! in_array($status, ['published', 'archived'], true)) {
            return ['status' => 'บทความที่เผยแพร่แล้วเปลี่ยนกลับได้เฉพาะ archived ผ่านฟอร์มแก้ไข'];
        }

        return [];
    }

    private function validateCategories(array $validated): array
    {
        $categoryIds = collect($validated['category_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($categoryIds->isEmpty()) {
            return [];
        }

        $validCount = Category::query()
            ->whereIn('id', $categoryIds)
            ->where('type_key', 'article')
            ->count();

        if ($validCount !== $categoryIds->count()) {
            return ['category_ids' => 'หมวดหมู่บทความต้องเป็น type article เท่านั้น'];
        }

        return [];
    }

    private function validateCoverMedia(array $validated): array
    {
        if (empty($validated['cover_media_id'])) {
            return [];
        }

        $isValid = Media::query()
            ->where('id', (int) $validated['cover_media_id'])
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->exists();

        return $isValid
            ? []
            : ['cover_media_id' => 'ภาพปกต้องเป็นรูปภาพที่อัปโหลดสำเร็จแล้ว'];
    }

    private function validateDates(array $validated): array
    {
        $errors = [];
        $publishedAt = ! empty($validated['published_at']) ? new \DateTimeImmutable($validated['published_at']) : null;
        $scheduledAt = ! empty($validated['scheduled_at']) ? new \DateTimeImmutable($validated['scheduled_at']) : null;
        $expiredAt = ! empty($validated['expired_at']) ? new \DateTimeImmutable($validated['expired_at']) : null;

        if ($publishedAt && $publishedAt > new \DateTimeImmutable('now')) {
            $errors['published_at'] = 'published_at ต้องไม่อยู่ในอนาคต';
        }

        if ($scheduledAt && $expiredAt && $expiredAt <= $scheduledAt) {
            $errors['expired_at'] = 'expired_at ต้องมากกว่า scheduled_at';
        }

        if ($publishedAt && $expiredAt && $expiredAt <= $publishedAt) {
            $errors['expired_at'] = 'expired_at ต้องมากกว่า published_at';
        }

        return $errors;
    }
}
