<?php

namespace App\Services\Admin\Content\Temple;

use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TempleDataSyncService
{
    public function create(array $validated): Temple
    {
        return DB::transaction(function () use ($validated) {
            $content = Content::query()->create([
                'content_type' => 'temple',
                'title' => $validated['title'],
                'slug' => Str::slug($validated['slug']),
                'template_id' => $validated['template_id'] ?? null,
                'excerpt' => $validated['excerpt'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'is_featured' => (bool) ($validated['is_featured'] ?? false),
                'is_popular' => (bool) ($validated['is_popular'] ?? false),
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'published_at' => $validated['published_at'] ?? null,
                'created_by_admin_id' => auth('admin')->id(),
                'updated_by_admin_id' => auth('admin')->id(),
            ]);

            $temple = Temple::query()->create([
                'content_id' => $content->id,
                'temple_type' => $validated['temple_type'] ?? null,
                'sect' => $validated['sect'] ?? null,
                'architecture_style' => $validated['architecture_style'] ?? null,
                'founded_year' => $validated['founded_year'] ?? null,
                'history' => $validated['history'] ?? null,
                'dress_code' => $validated['dress_code'] ?? null,
                'recommended_visit_start_time' => $validated['recommended_visit_start_time'] ?? null,
                'recommended_visit_end_time' => $validated['recommended_visit_end_time'] ?? null,   
            ]);

            $this->syncAll($temple, $content, $validated);

            return $temple;
        });
    }

    public function update(Temple $temple, array $validated): Temple
    {
        $temple->load('content');

        DB::transaction(function () use ($temple, $validated) {
            $temple->content->update([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['slug']),
                'template_id' => $validated['template_id'] ?? null,
                'excerpt' => $validated['excerpt'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'is_featured' => (bool) ($validated['is_featured'] ?? false),
                'is_popular' => (bool) ($validated['is_popular'] ?? false),
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'published_at' => $validated['published_at'] ?? null,
                'updated_by_admin_id' => auth('admin')->id(),
            ]);

            $temple->update([
                'temple_type' => $validated['temple_type'] ?? null,
                'sect' => $validated['sect'] ?? null,
                'architecture_style' => $validated['architecture_style'] ?? null,
                'founded_year' => $validated['founded_year'] ?? null,
                'history' => $validated['history'] ?? null,
                'dress_code' => $validated['dress_code'] ?? null,
                'recommended_visit_start_time' => $validated['recommended_visit_start_time'] ?? null,
                'recommended_visit_end_time' => $validated['recommended_visit_end_time'] ?? null,
            ]);

            $this->syncAll($temple, $temple->content, $validated);
        });

        return $temple;
    }

    public function delete(Temple $temple): void
    {
        $temple->load('content');

        DB::transaction(function () use ($temple) {
            $temple->content?->categories()->detach();
            $temple->content?->mediaUsages()->delete();
            $temple->content?->delete();
        });
    }

    private function syncAll(Temple $temple, Content $content, array $validated): void
    {
        $this->syncAddress($temple, $validated['address'] ?? []);
        $this->syncCategories($content, $validated);
        $this->syncMediaUsages($content, $validated);
        $this->syncOpeningHours($temple, $validated['opening_hours'] ?? []);
        $this->syncFees($temple, $validated['fees'] ?? []);
        $this->syncFacilityItems($temple, $validated['facility_items'] ?? []);
        $this->syncHighlights($temple, $validated['highlights'] ?? []);
        $this->syncVisitRules($temple, $validated['visit_rules'] ?? []);
        $this->syncTravelInfos($temple, $validated['travel_infos'] ?? []);
        $this->syncNearbyPlaces($temple, $validated['nearby_places'] ?? []);
    }

    private function syncAddress(Temple $temple, array $address): void
    {
        $hasAddressData = collect($address)->filter(function ($value) {
            return $value !== null && $value !== '';
        })->isNotEmpty();

        if (! $hasAddressData) {
            $temple->address()?->delete();
            return;
        }

        $temple->address()->updateOrCreate(
            ['temple_id' => $temple->id],
            [
                'address_line' => $address['address_line'] ?? null,
                'province' => $address['province'] ?? null,
                'district' => $address['district'] ?? null,
                'subdistrict' => $address['subdistrict'] ?? null,
                'postal_code' => $address['postal_code'] ?? null,
                'latitude' => $address['latitude'] ?? null,
                'longitude' => $address['longitude'] ?? null,
                'google_place_id' => $address['google_place_id'] ?? null,
                'google_maps_url' => $address['google_maps_url'] ?? null,
            ]
        );
    }

    private function syncCategories(Content $content, array $validated): void
    {
        $categoryIds = collect($validated['category_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($categoryIds->isEmpty()) {
            $content->categories()->detach();
            return;
        }

        $primaryCategoryId = ! empty($validated['primary_category_id'])
            ? (int) $validated['primary_category_id']
            : null;

        $syncData = [];

        foreach ($categoryIds as $index => $categoryId) {
            $syncData[$categoryId] = [
                'is_primary' => $primaryCategoryId === $categoryId,
                'sort_order' => $index,
                'created_at' => now(),
            ];
        }

        $content->categories()->sync($syncData);
    }

    private function syncMediaUsages(Content $content, array $validated): void
    {
        $content->mediaUsages()->delete();

        $rows = [];

        if (! empty($validated['cover_media_id'])) {
            $rows[] = [
                'media_id' => (int) $validated['cover_media_id'],
                'entity_type' => Content::class,
                'entity_id' => $content->id,
                'role_key' => 'cover',
                'sort_order' => 0,
                'created_by_admin_id' => auth('admin')->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (collect($validated['gallery_media_ids'] ?? [])->unique()->values() as $index => $mediaId) {
            if (! empty($validated['cover_media_id']) && (int) $validated['cover_media_id'] === (int) $mediaId) {
                continue;
            }

            $rows[] = [
                'media_id' => (int) $mediaId,
                'entity_type' => Content::class,
                'entity_id' => $content->id,
                'role_key' => 'gallery',
                'sort_order' => $index,
                'created_by_admin_id' => auth('admin')->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($rows)) {
            $content->mediaUsages()->createMany($rows);
        }
    }

    private function syncOpeningHours(Temple $temple, array $rows): void
    {
        $temple->openingHours()->delete();

        $items = collect($rows)
            ->filter(fn ($item) => ! empty($item['day_of_week']) || $item['day_of_week'] === 0)
            ->map(function ($item) use ($temple) {
                return [
                    'temple_id' => $temple->id,
                    'day_of_week' => (int) $item['day_of_week'],
                    'open_time' => ! empty($item['open_time']) ? $item['open_time'] . ':00' : null,
                    'close_time' => ! empty($item['close_time']) ? $item['close_time'] . ':00' : null,
                    'is_closed' => (bool) ($item['is_closed'] ?? false),
                    'note' => $item['note'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->sortBy('day_of_week')
            ->values()
            ->all();

        if (! empty($items)) {
            $temple->openingHours()->createMany($items);
        }
    }

    private function syncFees(Temple $temple, array $rows): void
    {
        $temple->fees()->delete();

        $items = collect($rows)
            ->filter(fn ($item) => ! empty($item['fee_type']) && ! empty($item['label']))
            ->map(function ($item) use ($temple) {
                return [
                    'temple_id' => $temple->id,
                    'fee_type' => $item['fee_type'],
                    'label' => $item['label'],
                    'amount' => $item['amount'] ?? null,
                    'currency' => $item['currency'] ?? 'THB',
                    'note' => $item['note'] ?? null,
                    'is_active' => (bool) ($item['is_active'] ?? false),
                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->all();

        if (! empty($items)) {
            $temple->fees()->createMany($items);
        }
    }

    private function syncFacilityItems(Temple $temple, array $rows): void
    {
        $temple->facilityItems()->delete();

        $items = collect($rows)
            ->filter(fn ($item) => ! empty($item['facility_id']))
            ->unique('facility_id')
            ->map(function ($item) use ($temple) {
                return [
                    'temple_id' => $temple->id,
                    'facility_id' => (int) $item['facility_id'],
                    'value' => $item['value'] ?? null,
                    'note' => $item['note'] ?? null,
                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->all();

        if (! empty($items)) {
            $temple->facilityItems()->createMany($items);
        }
    }

    private function syncHighlights(Temple $temple, array $rows): void
    {
        $temple->highlights()->delete();

        $items = collect($rows)
            ->filter(fn ($item) => ! empty($item['title']))
            ->map(function ($item) use ($temple) {
                return [
                    'temple_id' => $temple->id,
                    'title' => $item['title'],
                    'description' => $item['description'] ?? null,
                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->all();

        if (! empty($items)) {
            $temple->highlights()->createMany($items);
        }
    }

    private function syncVisitRules(Temple $temple, array $rows): void
    {
        $temple->visitRules()->delete();

        $items = collect($rows)
            ->filter(fn ($item) => ! empty($item['rule_text']))
            ->map(function ($item) use ($temple) {
                return [
                    'temple_id' => $temple->id,
                    'rule_text' => $item['rule_text'],
                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->all();

        if (! empty($items)) {
            $temple->visitRules()->createMany($items);
        }
    }

    private function syncTravelInfos(Temple $temple, array $rows): void
    {
        $temple->travelInfos()->delete();

        $items = collect($rows)
            ->filter(fn ($item) => ! empty($item['travel_type']))
            ->map(function ($item) use ($temple) {
                return [
                    'temple_id' => $temple->id,
                    'travel_type' => $item['travel_type'],
                    'start_place' => $item['start_place'] ?? null,
                    'distance_km' => $item['distance_km'] ?? null,
                    'duration_minutes' => $item['duration_minutes'] ?? null,
                    'cost_estimate' => $item['cost_estimate'] ?? null,
                    'note' => $item['note'] ?? null,
                    'is_active' => (bool) ($item['is_active'] ?? false),
                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->all();

        if (! empty($items)) {
            $temple->travelInfos()->createMany($items);
        }
    }

    private function syncNearbyPlaces(Temple $temple, array $rows): void
    {
        $temple->nearbyPlaces()->delete();

        $items = collect($rows)
            ->filter(function ($item) use ($temple) {
                if (empty($item['nearby_temple_id'])) {
                    return false;
                }

                return (int) $item['nearby_temple_id'] !== (int) $temple->id;
            })
            ->unique('nearby_temple_id')
            ->map(function ($item) use ($temple) {
                return [
                    'temple_id' => $temple->id,
                    'nearby_temple_id' => (int) $item['nearby_temple_id'],
                    'relation_type' => $item['relation_type'] ?? null,
                    'distance_km' => $item['distance_km'] ?? null,
                    'duration_minutes' => $item['duration_minutes'] ?? null,
                    'score' => $item['score'] ?? null,
                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->all();

        if (! empty($items)) {
            $temple->nearbyPlaces()->createMany($items);
        }
    }
}
