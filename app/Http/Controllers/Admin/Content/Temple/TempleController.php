<?php

namespace App\Http\Controllers\Admin\Content\Temple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Temple\StoreTempleRequest;
use App\Http\Requests\Admin\Content\Temple\UpdateTempleRequest;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Media\Media;
use App\Models\Content\Temple\Facility;
use App\Models\Content\Temple\Temple;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TempleController extends Controller
{
    private const STATUS_OPTIONS = [
        'draft',
        'published',
        'archived',
    ];

    public function index(Request $request): View
    {
        $query = Temple::query()
            ->with([
                'content',
                'content.categories',
                'content.mediaUsages.media',
                'address',
                'stat',
            ])
            ->whereHas('content', function ($query) use ($request) {
                if ($request->filled('search')) {
                    $search = $request->string('search')->toString();

                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%')
                            ->orWhere('excerpt', 'like', '%' . $search . '%');
                    });
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->string('status')->toString());
                }
            });

        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');

            $query->whereHas('content.categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        if ($request->filled('sort')) {
            $sort = $request->string('sort')->toString();

            if ($sort === 'popular') {
                $query->leftJoin('temple_stats', 'temple_stats.temple_id', '=', 'temples.id')
                    ->select('temples.*')
                    ->orderByDesc('temple_stats.score')
                    ->orderByDesc('temple_stats.favorite_count')
                    ->orderByDesc('temple_stats.review_count');
            } elseif ($sort === 'oldest') {
                $query->orderBy('id');
            } else {
                $query->latest('id');
            }
        } else {
            $query->latest('id');
        }

        $temples = $query->paginate(15)->withQueryString();

        $categories = Category::query()
            ->where('type_key', 'temple')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return view('admin.content.temples.index', [
            'title' => 'Temple Management',
            'temples' => $temples,
            'categories' => $categories,
            'statuses' => self::STATUS_OPTIONS,
        ]);
    }

    public function create(): View
    {
        return view('admin.content.temples.create', [
            'title' => 'Create Temple',
            'statusOptions' => self::STATUS_OPTIONS,
            'categories' => Category::query()
                ->where('type_key', 'temple')
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id']),
            'mediaItems' => Media::query()
                ->where('upload_status', 'completed')
                ->orderByDesc('id')
                ->get(['id', 'title', 'original_filename', 'media_type', 'path']),
            'facilities' => Facility::query()
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name']),
            'nearbyTemples' => Temple::query()
                ->with('content:id,title')
                ->whereHas('content')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function store(StoreTempleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, &$temple) {
            $content = Content::query()->create([
                'content_type' => 'temple',
                'title' => $validated['title'],
                'slug' => Str::slug($validated['slug']),
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
                'recommended_visit_duration_minutes' => $validated['recommended_visit_duration_minutes'] ?? null,
            ]);

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
        });

        return redirect()
            ->route('admin.temples.index')
            ->with('success', 'สร้างข้อมูลวัดเรียบร้อยแล้ว');
    }

    public function show(Temple $temple): View
    {
        $temple->load([
            'content.categories',
            'content.mediaUsages.media',
            'address',
            'openingHours',
            'fees',
            'facilityItems.facility',
            'highlights',
            'visitRules',
            'travelInfos',
            'nearbyPlaces.nearbyTemple.content',
            'stat',
        ]);

        return view('admin.content.temples.show', [
            'title' => 'Temple Detail',
            'temple' => $temple,
        ]);
    }

    public function edit(Temple $temple): View
    {
        $temple->load([
            'content.categories',
            'content.mediaUsages',
            'address',
            'openingHours',
            'fees',
            'facilityItems',
            'highlights',
            'visitRules',
            'travelInfos',
            'nearbyPlaces',
        ]);

        return view('admin.content.temples.edit', [
            'title' => 'Edit Temple',
            'temple' => $temple,
            'statusOptions' => self::STATUS_OPTIONS,
            'categories' => Category::query()
                ->where('type_key', 'temple')
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id']),
            'mediaItems' => Media::query()
                ->where('upload_status', 'completed')
                ->orderByDesc('id')
                ->get(['id', 'title', 'original_filename', 'media_type', 'path']),
            'facilities' => Facility::query()
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name']),
            'nearbyTemples' => Temple::query()
                ->with('content:id,title')
                ->where('id', '!=', $temple->id)
                ->whereHas('content')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function update(UpdateTempleRequest $request, Temple $temple): RedirectResponse
    {
        $validated = $request->validated();

        $temple->load('content');

        DB::transaction(function () use ($temple, $validated) {
            $temple->content->update([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['slug']),
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
                'recommended_visit_duration_minutes' => $validated['recommended_visit_duration_minutes'] ?? null,
            ]);

            $this->syncAddress($temple, $validated['address'] ?? []);
            $this->syncCategories($temple->content, $validated);
            $this->syncMediaUsages($temple->content, $validated);
            $this->syncOpeningHours($temple, $validated['opening_hours'] ?? []);
            $this->syncFees($temple, $validated['fees'] ?? []);
            $this->syncFacilityItems($temple, $validated['facility_items'] ?? []);
            $this->syncHighlights($temple, $validated['highlights'] ?? []);
            $this->syncVisitRules($temple, $validated['visit_rules'] ?? []);
            $this->syncTravelInfos($temple, $validated['travel_infos'] ?? []);
            $this->syncNearbyPlaces($temple, $validated['nearby_places'] ?? []);
        });

        return redirect()
            ->route('admin.temples.edit', $temple)
            ->with('success', 'อัปเดตข้อมูลวัดเรียบร้อยแล้ว');
    }

    public function destroy(Temple $temple): RedirectResponse
    {
        $temple->load('content');

        DB::transaction(function () use ($temple) {
            $temple->content?->categories()->detach();
            $temple->content?->mediaUsages()->delete();
            $temple->content?->delete();
        });

        return redirect()
            ->route('admin.temples.index')
            ->with('success', 'ลบข้อมูลวัดเรียบร้อยแล้ว');
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