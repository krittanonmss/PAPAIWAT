<?php

namespace App\Http\Controllers\Admin\Content\Temple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Temple\StoreTempleRequest;
use App\Http\Requests\Admin\Content\Temple\UpdateTempleRequest;
use App\Models\Admin\AuditLog;
use App\Models\Content\Category;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use App\Models\Content\Temple\Facility;
use App\Models\Content\Temple\Temple;
use App\Services\Admin\Content\Temple\TempleDataSyncService;
use App\Services\Admin\Content\Temple\TempleValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TempleController extends Controller
{
    private const STATUS_OPTIONS = [
        'draft',
        'review',
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

        $temples = $query->paginate(5)->withQueryString();

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

        $this->templeDataSyncService->createVersion($temple, 'published');

        $temple->refresh()->load('content.mediaUsages');
        $newData = $this->templeAuditData($temple);

        $this->writeAuditLog($request, 'temple.published', $temple, $oldData, $newData);

        return redirect()
            ->route('admin.temples.edit', $temple)
            ->with('success', 'เผยแพร่ข้อมูลวัดเรียบร้อยแล้ว');
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
