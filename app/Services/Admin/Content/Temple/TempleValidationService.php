<?php

namespace App\Services\Admin\Content\Temple;

use App\Models\Content\Category;
use App\Models\Content\Media\Media;
use App\Models\Content\Temple\Facility;
use App\Models\Content\Temple\Temple;
use Illuminate\Validation\ValidationException;

class TempleValidationService
{
    public function validateForSave(array $validated, ?Temple $temple = null): void
    {
        $errors = [];

        $errors += $this->validateStatus($validated, $temple);
        $errors += $this->validateCategories($validated);
        $errors += $this->validateMedia($validated);
        $errors += $this->validateFacilities($validated['facility_items'] ?? []);
        $errors += $this->validateOpeningHours($validated['opening_hours'] ?? []);
        $errors += $this->validateFees($validated['fees'] ?? []);
        $errors += $this->validateNearbyPlaces($validated['nearby_places'] ?? [], $temple);

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function validateForPublish(Temple $temple): void
    {
        $temple->loadMissing('content.categories', 'openingHours');

        $errors = [];

        if (! in_array($temple->content?->status, ['draft', 'review'], true)) {
            $errors['status'] = 'เผยแพร่ได้เฉพาะเนื้อหาที่อยู่ในสถานะ draft หรือ review';
        }

        if (! $temple->content?->title || ! $temple->content?->slug) {
            $errors['title'] = 'ต้องมีชื่อและ slug ก่อนเผยแพร่';
        }

        if ($temple->content?->categories->isEmpty()) {
            $errors['category_ids'] = 'ต้องเลือกหมวดหมู่อย่างน้อย 1 รายการก่อนเผยแพร่';
        }

        if ($temple->openingHours->isEmpty()) {
            $errors['opening_hours'] = 'ต้องมีเวลาเปิดทำการก่อนเผยแพร่';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function validateStatus(array $validated, ?Temple $temple): array
    {
        $status = $validated['status'] ?? 'draft';

        if ($status === 'published') {
            if ($temple?->content?->status === 'published') {
                return [];
            }

            if (! auth('admin')->user()?->hasPermission('temples.publish')) {
                return ['status' => 'ต้องมีสิทธิ์เผยแพร่จึงจะตั้งสถานะเป็นเผยแพร่ได้'];
            }

            $errors = [];

            if (empty($validated['category_ids'])) {
                $errors['category_ids'] = 'ต้องเลือกหมวดหมู่อย่างน้อย 1 รายการก่อนเผยแพร่';
            }

            if (empty($validated['opening_hours'])) {
                $errors['opening_hours'] = 'ต้องมีเวลาเปิดทำการก่อนเผยแพร่';
            }

            return $errors;
        }

        if (! $temple?->content || $temple->content->status !== 'published') {
            return [];
        }

        if (! in_array($status, ['published', 'archived'], true)) {
            return ['status' => 'เนื้อหาที่เผยแพร่แล้วเปลี่ยนกลับได้เฉพาะ archived ผ่านฟอร์มแก้ไข'];
        }

        return [];
    }

    private function validateCategories(array $validated): array
    {
        $categoryIds = collect($validated['category_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $primaryCategoryId = ! empty($validated['primary_category_id'])
            ? (int) $validated['primary_category_id']
            : null;

        if ($primaryCategoryId && ! $categoryIds->contains($primaryCategoryId)) {
            return ['primary_category_id' => 'หมวดหมู่หลักต้องอยู่ในรายการหมวดหมู่ที่เลือก'];
        }

        if ($categoryIds->isEmpty()) {
            return [];
        }

        $validCount = Category::query()
            ->whereIn('id', $categoryIds)
            ->where('type_key', 'temple')
            ->where('status', 'active')
            ->count();

        if ($validCount !== $categoryIds->count()) {
            return ['category_ids' => 'หมวดหมู่ของวัดต้องเป็น type temple และ active เท่านั้น'];
        }

        return [];
    }

    private function validateFacilities(array $rows): array
    {
        $facilityIds = collect($rows)
            ->pluck('facility_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($facilityIds->isEmpty()) {
            return [];
        }

        $validCount = Facility::query()
            ->whereIn('id', $facilityIds)
            ->where('type_key', 'temple')
            ->where('status', 'active')
            ->count();

        return $validCount === $facilityIds->count()
            ? []
            : ['facility_items' => 'สิ่งอำนวยความสะดวกต้องเป็น type temple และ active เท่านั้น'];
    }

    private function validateMedia(array $validated): array
    {
        $mediaIds = collect([$validated['cover_media_id'] ?? null])
            ->merge($validated['gallery_media_ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($mediaIds->isEmpty()) {
            return [];
        }

        $validCount = Media::query()
            ->whereIn('id', $mediaIds)
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->count();

        if ($validCount !== $mediaIds->count()) {
            return ['cover_media_id' => 'ไฟล์ภาพปกและแกลเลอรีต้องเป็นรูปภาพที่อัปโหลดสำเร็จแล้ว'];
        }

        return [];
    }

    private function validateOpeningHours(array $rows): array
    {
        $errors = [];
        $days = [];

        foreach ($rows as $index => $row) {
            if (! array_key_exists('day_of_week', $row) || $row['day_of_week'] === '') {
                continue;
            }

            $day = (int) $row['day_of_week'];

            if (in_array($day, $days, true)) {
                $errors["opening_hours.{$index}.day_of_week"] = 'วันเปิดทำการซ้ำกัน';
            }

            $days[] = $day;

            if ((bool) ($row['is_closed'] ?? false)) {
                continue;
            }

            $openTime = $row['open_time'] ?? null;
            $closeTime = $row['close_time'] ?? null;

            if (! $openTime || ! $closeTime) {
                $errors["opening_hours.{$index}.open_time"] = 'วันที่เปิดทำการต้องมีเวลาเปิดและเวลาปิด';
                continue;
            }

            if ($openTime >= $closeTime) {
                $errors["opening_hours.{$index}.close_time"] = 'เวลาปิดต้องมากกว่าเวลาเปิด';
            }
        }

        return $errors;
    }

    private function validateFees(array $rows): array
    {
        $errors = [];
        $keys = [];

        foreach ($rows as $index => $row) {
            if (empty($row['fee_type']) && empty($row['label'])) {
                continue;
            }

            $key = strtolower(trim((string) ($row['fee_type'] ?? '')).'|'.trim((string) ($row['label'] ?? '')));

            if (in_array($key, $keys, true)) {
                $errors["fees.{$index}.label"] = 'รายการค่าธรรมเนียมซ้ำกัน';
            }

            $keys[] = $key;

            if (($row['amount'] ?? null) !== null && (float) $row['amount'] > 0 && empty($row['currency'])) {
                $errors["fees.{$index}.currency"] = 'กรุณาระบุสกุลเงินเมื่อมีจำนวนเงิน';
            }
        }

        return $errors;
    }

    private function validateNearbyPlaces(array $rows, ?Temple $temple): array
    {
        $errors = [];
        $templeIds = [];

        foreach ($rows as $index => $row) {
            if (empty($row['nearby_temple_id'])) {
                continue;
            }

            $nearbyTempleId = (int) $row['nearby_temple_id'];

            if ($temple && $nearbyTempleId === (int) $temple->id) {
                $errors["nearby_places.{$index}.nearby_temple_id"] = 'ไม่สามารถเลือกวัดเดียวกันเป็นสถานที่ใกล้เคียงได้';
            }

            if (in_array($nearbyTempleId, $templeIds, true)) {
                $errors["nearby_places.{$index}.nearby_temple_id"] = 'วัดใกล้เคียงซ้ำกัน';
            }

            $templeIds[] = $nearbyTempleId;
        }

        $validNearbyCount = Temple::query()
            ->whereIn('id', $templeIds)
            ->whereHas('content', fn ($query) => $query
                ->where('content_type', 'temple')
                ->whereNull('deleted_at'))
            ->count();

        if ($validNearbyCount !== count(array_unique($templeIds))) {
            $errors['nearby_places'] = 'วัดใกล้เคียงต้องเป็นวัดที่ยังใช้งานอยู่';
        }

        return $errors;
    }
}
