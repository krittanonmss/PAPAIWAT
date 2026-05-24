<?php

namespace App\Http\Controllers\Admin\Content\Temple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Temple\StoreTempleRequest;
use App\Http\Requests\Admin\Content\Temple\UpdateTempleRequest;
use App\Models\Admin\AuditLog;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use App\Models\Content\Temple\Facility;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleAddress;
use App\Services\Admin\AdminNotificationService;
use App\Services\Admin\AdminPreferenceService;
use App\Services\Admin\Content\Temple\TempleDataSyncService;
use App\Services\Admin\Content\Temple\TempleValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TempleController extends Controller
{
    private const STATUS_OPTIONS = [
        'draft',
        'review',
        'archived',
    ];

    private const INDEX_STATUS_OPTIONS = [
        'draft',
        'review',
        'published',
        'archived',
    ];

    public function __construct(
        private readonly TempleDataSyncService $templeDataSyncService,
        private readonly TempleValidationService $templeValidationService,
    ) {
    }

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
            ->whereHas('content');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('temple_type', 'like', '%' . $search . '%')
                    ->orWhere('sect', 'like', '%' . $search . '%')
                    ->orWhere('architecture_style', 'like', '%' . $search . '%')
                    ->orWhereHas('content', function ($contentQuery) use ($search) {
                        $contentQuery->where('title', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%')
                            ->orWhere('excerpt', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('address', function ($addressQuery) use ($search) {
                        $addressQuery->where('address_line', 'like', '%' . $search . '%')
                            ->orWhere('province', 'like', '%' . $search . '%')
                            ->orWhere('district', 'like', '%' . $search . '%')
                            ->orWhere('subdistrict', 'like', '%' . $search . '%')
                            ->orWhere('postal_code', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('content', function ($q) use ($request) {
                $q->where('status', $request->string('status')->toString());
            });
        }

        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');

            $query->whereHas('content.categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        if ($request->filled('template_id')) {
            $templateId = (int) $request->input('template_id');

            $query->whereHas('content', fn ($q) => $q->where('template_id', $templateId));
        }

        if ($request->filled('featured')) {
            $featured = $request->string('featured')->toString();

            if (in_array($featured, ['yes', 'no'], true)) {
                $query->whereHas('content', fn ($q) => $q->where('is_featured', $featured === 'yes'));
            }
        }

        if ($request->filled('temple_type')) {
            $query->where('temple_type', $request->string('temple_type')->toString());
        }

        if ($request->filled('sect')) {
            $query->where('sect', $request->string('sect')->toString());
        }

        if ($request->filled('province')) {
            $query->whereHas('address', fn ($q) => $q->where('province', $request->string('province')->toString()));
        }

        if ($request->filled('district')) {
            $query->whereHas('address', fn ($q) => $q->where('district', $request->string('district')->toString()));
        }

        if ($request->filled('has_location')) {
            $locationFilter = $request->string('has_location')->toString();

            if ($locationFilter === 'yes') {
                $query->whereHas('address', fn ($q) => $q->whereNotNull('latitude')->whereNotNull('longitude'));
            } elseif ($locationFilter === 'no') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('address')
                        ->orWhereHas('address', fn ($addressQuery) => $addressQuery->whereNull('latitude')->orWhereNull('longitude'));
                });
            }
        }

        if ($request->filled('has_media')) {
            $mediaFilter = $request->string('has_media')->toString();

            if ($mediaFilter === 'cover') {
                $query->whereHas('content.mediaUsages', fn ($q) => $q->where('role_key', 'cover'));
            } elseif ($mediaFilter === 'missing_cover') {
                $query->whereDoesntHave('content.mediaUsages', fn ($q) => $q->where('role_key', 'cover'));
            } elseif ($mediaFilter === 'gallery') {
                $query->whereHas('content.mediaUsages', fn ($q) => $q->where('role_key', 'gallery'));
            } elseif ($mediaFilter === 'missing_gallery') {
                $query->whereDoesntHave('content.mediaUsages', fn ($q) => $q->where('role_key', 'gallery'));
            } elseif ($mediaFilter === 'any') {
                $query->whereHas('content.mediaUsages');
            } elseif ($mediaFilter === 'none') {
                $query->whereDoesntHave('content.mediaUsages');
            }
        }

        if ($request->filled('published_from')) {
            $publishedFrom = $this->dateInput($request, 'published_from');

            if ($publishedFrom) {
                $query->whereHas('content', fn ($q) => $q->whereDate('published_at', '>=', $publishedFrom));
            }
        }

        if ($request->filled('published_to')) {
            $publishedTo = $this->dateInput($request, 'published_to');

            if ($publishedTo) {
                $query->whereHas('content', fn ($q) => $q->whereDate('published_at', '<=', $publishedTo));
            }
        }

        if ($request->filled('created_from')) {
            $createdFrom = $this->dateInput($request, 'created_from');

            if ($createdFrom) {
                $query->whereDate('created_at', '>=', $createdFrom);
            }
        }

        if ($request->filled('created_to')) {
            $createdTo = $this->dateInput($request, 'created_to');

            if ($createdTo) {
                $query->whereDate('created_at', '<=', $createdTo);
            }
        }

        if ($request->filled('sort')) {
            $sort = $request->string('sort')->toString();

            if ($sort === 'popular') {
                $query->leftJoin('temple_stats', 'temple_stats.temple_id', '=', 'temples.id')
                    ->select('temples.*')
                    ->orderByDesc('temple_stats.score')
                    ->orderByDesc('temple_stats.favorite_count')
                    ->orderByDesc('temple_stats.review_count');
            } elseif ($sort === 'reviews') {
                $query->leftJoin('temple_stats', 'temple_stats.temple_id', '=', 'temples.id')
                    ->select('temples.*')
                    ->orderByDesc('temple_stats.review_count')
                    ->orderByDesc('temple_stats.average_rating');
            } elseif ($sort === 'favorites') {
                $query->leftJoin('temple_stats', 'temple_stats.temple_id', '=', 'temples.id')
                    ->select('temples.*')
                    ->orderByDesc('temple_stats.favorite_count');
            } elseif ($sort === 'title_asc') {
                $query->orderBy(Content::query()->select('title')->whereColumn('contents.id', 'temples.content_id'));
            } elseif ($sort === 'title_desc') {
                $query->orderByDesc(Content::query()->select('title')->whereColumn('contents.id', 'temples.content_id'));
            } elseif ($sort === 'published_newest') {
                $query->orderByDesc(Content::query()->select('published_at')->whereColumn('contents.id', 'temples.content_id'));
            } elseif ($sort === 'published_oldest') {
                $query->orderBy(Content::query()->select('published_at')->whereColumn('contents.id', 'temples.content_id'));
            } elseif ($sort === 'oldest') {
                $query->orderBy('id');
            } else {
                $query->latest('id');
            }
        } else {
            $query->latest('id');
        }

        $temples = $query->paginate($this->perPage($request, 5))->withQueryString();

        $categories = Category::query()
            ->where('type_key', 'temple')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return view('admin.content.temples.index', [
            'title' => 'Temple Management',
            'temples' => $temples,
            'categories' => $categories,
            'statuses' => self::INDEX_STATUS_OPTIONS,
            'detailTemplates' => $this->detailTemplates('temple'),
            'templeTypes' => $this->distinctTempleOptions('temple_type'),
            'sects' => $this->distinctTempleOptions('sect'),
            'provinces' => $this->distinctAddressOptions('province'),
            'districts' => $this->distinctAddressOptions('district'),
        ]);
    }

    public function create(): View
    {
        return view('admin.content.temples.create', [
            'title' => 'Create Temple',
            'statusOptions' => self::STATUS_OPTIONS,
            'categories' => collect(),
            'coverMediaItems' => $this->coverMediaItems(),
            'galleryMediaItems' => $this->galleryMediaItems(),
            'facilities' => collect(),
            'detailTemplates' => $this->detailTemplates('temple'),
            'templatePreviewUrl' => route('admin.content.template-preview.sample', ['type' => 'temple']),
            'templatePreviewLiveUrl' => route('admin.content.template-preview.live', ['type' => 'temple']),
            'nearbyTemples' => collect(),
        ]);
    }

    public function store(StoreTempleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->templeValidationService->validateForSave($validated);

        $temple = $this->templeDataSyncService->create($validated);
        $temple->load('content.mediaUsages');

        $this->writeAuditLog($request, 'temple.created', $temple, null, $this->templeAuditData($temple));

        if ($temple->content?->status === 'published') {
            $this->recordPublishedTransition($request, $temple, null);
        } else {
            $this->notifyReviewRequestedIfNeeded(null, $temple);
        }

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
            'facilityItems.facility',
            'highlights',
            'visitRules',
            'travelInfos',
            'nearbyPlaces.nearbyTemple.content',
        ]);

        return view('admin.content.temples.edit', [
            'title' => 'Edit Temple',
            'temple' => $temple,
            'statusOptions' => self::STATUS_OPTIONS,
            'categories' => $temple->content?->categories ?? collect(),
            'coverMediaItems' => $this->coverMediaItems([
                $temple->content?->mediaUsages?->firstWhere('role_key', 'cover')?->media_id,
            ]),
            'galleryMediaItems' => $this->galleryMediaItems(
                $temple->content?->mediaUsages?->where('role_key', 'gallery')->pluck('media_id')->all() ?? []
            ),
            'facilities' => collect(),
            'detailTemplates' => $this->detailTemplates('temple'),
            'templatePreviewUrl' => $temple->content
                ? route('admin.content.template-preview', ['type' => 'temple', 'content' => $temple->content])
                : route('admin.content.template-preview.sample', ['type' => 'temple']),
            'templatePreviewLiveUrl' => route('admin.content.template-preview.live', ['type' => 'temple']),
            'nearbyTemples' => collect(),
        ]);
    }

    public function update(UpdateTempleRequest $request, Temple $temple): RedirectResponse
    {
        $validated = $request->validated();

        $temple->load('content.mediaUsages');
        $oldData = $this->templeAuditData($temple);

        $this->templeValidationService->validateForSave($validated, $temple);
        $this->templeDataSyncService->update($temple, $validated);

        $temple->refresh()->load('content.mediaUsages');
        $newData = $this->templeAuditData($temple);

        $this->writeAuditLog($request, 'temple.updated', $temple, $oldData, $newData);

        if (($oldData['status'] ?? null) !== ($newData['status'] ?? null)) {
            $this->writeAuditLog($request, 'temple.status_changed', $temple, ['status' => $oldData['status']], ['status' => $newData['status']]);

            if (($newData['status'] ?? null) === 'published') {
                $this->recordPublishedTransition($request, $temple, $oldData);
            } else {
                $this->notifyReviewRequestedIfNeeded($oldData['status'] ?? null, $temple);
            }
        }

        if (($oldData['template_id'] ?? null) !== ($newData['template_id'] ?? null)) {
            $this->writeAuditLog($request, 'temple.template_changed', $temple, ['template_id' => $oldData['template_id']], ['template_id' => $newData['template_id']]);
        }

        return redirect()
            ->route('admin.temples.edit', $temple)
            ->with('success', 'อัปเดตข้อมูลวัดเรียบร้อยแล้ว');
    }

    public function publish(Request $request, Temple $temple): RedirectResponse
    {
        $temple->load('content.mediaUsages', 'content.categories', 'openingHours');
        $oldData = $this->templeAuditData($temple);

        $this->templeValidationService->validateForPublish($temple);

        $temple->content->forceFill([
            'status' => 'published',
            'published_at' => $temple->content->published_at ?? now(),
            'updated_by_admin_id' => auth('admin')->id(),
        ])->save();

        $this->recordPublishedTransition($request, $temple, $oldData);

        return redirect()
            ->route('admin.temples.edit', $temple)
            ->with('success', 'เผยแพร่ข้อมูลวัดเรียบร้อยแล้ว');
    }

    private function recordPublishedTransition(Request $request, Temple $temple, ?array $oldData): void
    {
        $this->templeDataSyncService->createVersion($temple, 'published');

        $temple->refresh()->load('content.mediaUsages');
        $this->writeAuditLog($request, 'temple.published', $temple, $oldData, $this->templeAuditData($temple));
        $this->notifyContent('เผยแพร่ข้อมูลวัดแล้ว', 'ข้อมูลวัด "'.($temple->content?->title ?? '#'.$temple->id).'" ถูกเผยแพร่แล้ว');
    }

    private function notifyContent(string $title, string $message): void
    {
        app(AdminNotificationService::class)->notifyAdmins('content', $title, $message);
    }

    private function notifyReviewRequestedIfNeeded(?string $oldStatus, Temple $temple): void
    {
        if ($oldStatus === 'review' || $temple->content?->status !== 'review') {
            return;
        }

        app(AdminNotificationService::class)->notifyAdminsWithPermission(
            'temples.publish',
            'content',
            'มีข้อมูลวัดรอตรวจสอบ',
            'ข้อมูลวัด "'.($temple->content?->title ?? '#'.$temple->id).'" รอผู้มีสิทธิ์เผยแพร่อนุมัติ'
        );
    }

    public function bulkAssignCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'temple_ids' => ['required', 'array', 'min:1'],
            'temple_ids.*' => ['integer', 'exists:temples,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ]);

        $category = Category::query()
            ->whereKey($validated['category_id'])
            ->where('type_key', 'temple')
            ->where('status', 'active')
            ->firstOrFail();

        $updatedCount = 0;

        DB::transaction(function () use ($request, $validated, $category, &$updatedCount): void {
            $temples = Temple::query()
                ->with('content.categories')
                ->whereIn('id', $validated['temple_ids'])
                ->whereHas('content', fn ($query) => $query
                    ->where('content_type', 'temple')
                    ->whereNull('deleted_at'))
                ->get();

            foreach ($temples as $temple) {
                if (! $temple->content) {
                    continue;
                }

                $categoryIds = $temple->content->categories->pluck('id')->map(fn ($id) => (int) $id);

                if ($categoryIds->contains((int) $category->id)) {
                    continue;
                }

                $oldData = $this->templeAuditData($temple);
                $hasPrimary = $temple->content->categories->contains(fn (Category $item) => (bool) $item->pivot?->is_primary);

                $temple->content->categories()->attach($category->id, [
                    'is_primary' => ! $hasPrimary && $categoryIds->isEmpty(),
                    'sort_order' => $categoryIds->count(),
                    'created_at' => now(),
                ]);

                $temple->content->update(['updated_by_admin_id' => auth('admin')->id()]);
                $temple->refresh()->load('content.mediaUsages', 'content.categories');

                $this->writeAuditLog($request, 'temple.category_assigned', $temple, $oldData, $this->templeAuditData($temple) + [
                    'assigned_category_id' => $category->id,
                ]);

                $updatedCount++;
            }
        });

        if ($updatedCount === 0) {
            return redirect()
                ->route('admin.temples.index')
                ->with('error', 'ไม่มีข้อมูลวัดที่ต้องเพิ่มเข้าหมวดหมู่นี้');
        }

        return redirect()
            ->route('admin.temples.index')
            ->with('success', 'เพิ่มข้อมูลวัดที่เลือกเข้าหมวดหมู่เรียบร้อยแล้ว');
    }

    public function destroy(Request $request, Temple $temple): RedirectResponse
    {
        $temple->load('content.mediaUsages');
        $oldData = $this->templeAuditData($temple);

        $this->templeDataSyncService->delete($temple);
        $this->writeAuditLog($request, 'temple.deleted', $temple, $oldData, ['deleted' => true]);

        return redirect()
            ->route('admin.temples.index')
            ->with('success', 'ลบข้อมูลวัดเรียบร้อยแล้ว');
    }

    public function coverMediaPicker(Request $request): View
    {
        return view('admin.content.temples.partials._cover_media_grid', [
            'mediaItems' => $this->coverMediaItems(search: $request->string('q')->toString()),
        ]);
    }

    public function galleryMediaPicker(Request $request): View
    {
        return view('admin.content.temples.partials._gallery_media_grid', [
            'mediaItems' => $this->galleryMediaItems(search: $request->string('q')->toString()),
        ]);
    }

    private function templeCategories()
    {
        return Category::query()
            ->where('type_key', 'temple')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);
    }

    private function coverMediaItems(array $selectedMediaIds = [], string $search = '')
    {
        return $this->paginatedMediaItems(
            pageName: 'cover_media_page',
            path: route('admin.temples.media-picker.cover'),
            perPage: 7,
            selectedMediaIds: $selectedMediaIds,
            search: $search
        );
    }

    private function galleryMediaItems(array $selectedMediaIds = [], string $search = '')
    {
        return $this->paginatedMediaItems(
            pageName: 'gallery_media_page',
            path: route('admin.temples.media-picker.gallery'),
            perPage: 8,
            selectedMediaIds: $selectedMediaIds,
            search: $search
        );
    }

    private function paginatedMediaItems(string $pageName, string $path, int $perPage, array $selectedMediaIds = [], string $search = '')
    {
        $mediaItems = Media::query()
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', '%' . $search . '%')
                        ->orWhere('original_filename', 'like', '%' . $search . '%')
                        ->orWhere('filename', 'like', '%' . $search . '%')
                        ->orWhere('id', $search);
                });
            })
            ->orderByDesc('id')
            ->paginate(
                perPage: $perPage,
                columns: ['id', 'title', 'original_filename', 'media_type', 'path'],
                pageName: $pageName
            )
            ->withPath($path)
            ->appends(array_filter(['q' => $search]));

        $selectedMediaIds = collect($selectedMediaIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($selectedMediaIds->isEmpty()) {
            return $mediaItems;
        }

        $visibleIds = $mediaItems->getCollection()->pluck('id')->map(fn ($id) => (int) $id);
        $missingSelectedIds = $selectedMediaIds->diff($visibleIds)->values();

        if ($missingSelectedIds->isEmpty()) {
            return $mediaItems;
        }

        $selectedItems = Media::query()
            ->whereIn('id', $missingSelectedIds)
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->get(['id', 'title', 'original_filename', 'media_type', 'path'])
            ->sortBy(fn (Media $media) => $selectedMediaIds->search((int) $media->id))
            ->values();

        $mediaItems->setCollection(
            $selectedItems
                ->concat($mediaItems->getCollection())
                ->unique('id')
                ->values()
        );

        return $mediaItems;
    }

    private function facilities()
    {
        return Facility::query()
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function detailTemplates(string $contentType)
    {
        return Template::query()
            ->active()
            ->where('template_type', 'detail')
            ->where('content_type', $contentType)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function distinctTempleOptions(string $column)
    {
        return Temple::query()
            ->whereNotNull($column)
            ->where($column, '<>', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column);
    }

    private function distinctAddressOptions(string $column)
    {
        return TempleAddress::query()
            ->whereNotNull($column)
            ->where($column, '<>', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column);
    }

    private function dateInput(Request $request, string $key): ?string
    {
        try {
            return $request->date($key)?->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function perPage(Request $request, int $default): int
    {
        $allowed = AdminPreferenceService::PER_PAGE_OPTIONS;
        $default = app(AdminPreferenceService::class)->preferredPerPage($request->user('admin'), $allowed, $default);
        $perPage = (int) $request->input('per_page', $default);

        return in_array($perPage, $allowed, true) ? $perPage : $default;
    }

    private function templeAuditData(Temple $temple): array
    {
        $content = $temple->content;

        return [
            'temple_id' => $temple->id,
            'content_id' => $temple->content_id,
            'title' => $content?->title,
            'slug' => $content?->slug,
            'template_id' => $content?->template_id,
            'status' => $content?->status,
            'published_at' => $content?->published_at?->toDateTimeString(),
            'cover_media_id' => $content?->mediaUsages?->firstWhere('role_key', 'cover')?->media_id,
            'gallery_media_ids' => $content?->mediaUsages?->where('role_key', 'gallery')->pluck('media_id')->values()->all() ?? [],
            'is_featured' => $content?->is_featured,
            'is_popular' => $content?->is_popular,
        ];
    }

    private function writeAuditLog(Request $request, string $action, Temple $temple, ?array $oldData, ?array $newData): void
    {
        AuditLog::query()->create([
            'action' => $action,
            'table_name' => 'temples',
            'record_id' => $temple->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'performed_by' => auth('admin')->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }
}
