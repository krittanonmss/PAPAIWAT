<?php

namespace App\Http\Controllers\Admin\Content\Temple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Temple\StoreTempleRequest;
use App\Http\Requests\Admin\Content\Temple\UpdateTempleRequest;
use App\Models\Content\Category;
use App\Models\Content\Layout\Template;
use App\Models\Content\Media\Media;
use App\Models\Content\Temple\Facility;
use App\Models\Content\Temple\Temple;
use App\Services\Admin\Content\Temple\TempleDataSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TempleController extends Controller
{
    private const STATUS_OPTIONS = [
        'draft',
        'published',
        'archived',
    ];

    public function __construct(
        private readonly TempleDataSyncService $templeDataSyncService
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
            'categories' => $this->templeCategories(),
            'coverMediaItems' => $this->coverMediaItems(),
            'galleryMediaItems' => $this->galleryMediaItems(),
            'facilities' => $this->facilities(),
            'detailTemplates' => $this->detailTemplates('temple'),
            'templatePreviewUrl' => route('admin.content.template-preview.sample', ['type' => 'temple']),
            'nearbyTemples' => Temple::query()
                ->with('content:id,title')
                ->whereHas('content')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function store(StoreTempleRequest $request): RedirectResponse
    {
        $this->templeDataSyncService->create($request->validated());

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
            'categories' => $this->templeCategories(),
            'coverMediaItems' => $this->coverMediaItems(),
            'galleryMediaItems' => $this->galleryMediaItems(),
            'facilities' => $this->facilities(),
            'detailTemplates' => $this->detailTemplates('temple'),
            'templatePreviewUrl' => $temple->content
                ? route('admin.content.template-preview', ['type' => 'temple', 'content' => $temple->content])
                : route('admin.content.template-preview.sample', ['type' => 'temple']),
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
        $this->templeDataSyncService->update($temple, $request->validated());

        return redirect()
            ->route('admin.temples.edit', $temple)
            ->with('success', 'อัปเดตข้อมูลวัดเรียบร้อยแล้ว');
    }

    public function destroy(Temple $temple): RedirectResponse
    {
        $this->templeDataSyncService->delete($temple);

        return redirect()
            ->route('admin.temples.index')
            ->with('success', 'ลบข้อมูลวัดเรียบร้อยแล้ว');
    }

    public function coverMediaPicker(Request $request): View
    {
        return view('admin.content.temples.partials._cover_media_grid', [
            'mediaItems' => $this->coverMediaItems(),
        ]);
    }

    public function galleryMediaPicker(Request $request): View
    {
        return view('admin.content.temples.partials._gallery_media_grid', [
            'mediaItems' => $this->galleryMediaItems(),
        ]);
    }

    private function templeCategories()
    {
        return Category::query()
            ->where('type_key', 'temple')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);
    }

    private function coverMediaItems()
    {
        return $this->paginatedMediaItems(
            pageName: 'cover_media_page',
            path: route('admin.temples.media-picker.cover'),
            perPage: 7
        );
    }

    private function galleryMediaItems()
    {
        return $this->paginatedMediaItems(
            pageName: 'gallery_media_page',
            path: route('admin.temples.media-picker.gallery'),
            perPage: 8
        );
    }

    private function paginatedMediaItems(string $pageName, string $path, int $perPage)
    {
        return Media::query()
            ->where('upload_status', 'completed')
            ->where('media_type', 'image')
            ->orderByDesc('id')
            ->paginate(
                perPage: $perPage,
                columns: ['id', 'title', 'original_filename', 'media_type', 'path'],
                pageName: $pageName
            )
            ->withPath($path);
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
            ->where('view_path', 'like', 'frontend.templates.details.%')
            ->where(function ($query) use ($contentType) {
                $query->where('key', $contentType . '-detail')
                    ->orWhere('view_path', 'like', 'frontend.templates.details.' . $contentType . '-%');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
