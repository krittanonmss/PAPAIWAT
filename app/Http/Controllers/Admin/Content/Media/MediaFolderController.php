<?php

namespace App\Http\Controllers\Admin\Content\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Media\StoreMediaFolderRequest;
use App\Http\Requests\Admin\Content\Media\UpdateMediaFolderRequest;
use App\Models\Content\Media\MediaFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaFolderController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = MediaFolder::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->string('status')->toString());
        }

        if ($request->filled('parent_id')) {
            if ($request->string('parent_id')->toString() === 'root') {
                $baseQuery->whereNull('parent_id');
            } else {
                $baseQuery->where('parent_id', (int) $request->input('parent_id'));
            }
        }

        $folderIds = $baseQuery->pluck('id');

        $folders = MediaFolder::query()
            ->with([
                'parent:id,name',
                'children' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('name');
                },
                'children.parent:id,name',
                'children.children' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('name');
                },
                'children.children.parent:id,name',
                'children.children.children' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('name');
                },
                'children.children.children.parent:id,name',
            ])
            ->when(
                $folderIds->isNotEmpty(),
                fn ($query) => $query->whereIn('id', $folderIds),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $parents = MediaFolder::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $stats = [
            'total' => MediaFolder::query()->count(),
            'active' => MediaFolder::query()->where('status', 'active')->count(),
            'inactive' => MediaFolder::query()->where('status', 'inactive')->count(),
            'root' => MediaFolder::query()->whereNull('parent_id')->count(),
        ];

        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
            'parent_id' => $request->string('parent_id')->toString(),
        ];

        return view('admin.content.media.folders.index', [
            'title' => 'Media Folder Management',
            'folders' => $folders,
            'parents' => $parents,
            'stats' => $stats,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $parents = MediaFolder::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.content.media.folders.create', [
            'title' => 'Create Media Folder',
            'parents' => $parents,
        ]);
    }

    public function store(StoreMediaFolderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $folder = MediaFolder::query()->create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
            'created_by_admin_id' => auth('admin')->id(),
            'updated_by_admin_id' => auth('admin')->id(),
        ]);

        return redirect()
            ->route('admin.media-folders.edit', $folder)
            ->with('success', 'สร้างโฟลเดอร์สื่อเรียบร้อยแล้ว');
    }

    public function edit(MediaFolder $mediaFolder): View
    {
        $parents = MediaFolder::query()
            ->where('id', '!=', $mediaFolder->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.content.media.folders.edit', [
            'title' => 'Edit Media Folder',
            'folder' => $mediaFolder,
            'parents' => $parents,
        ]);
    }

    public function update(UpdateMediaFolderRequest $request, MediaFolder $mediaFolder): RedirectResponse
    {
        $validated = $request->validated();

        $mediaFolder->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
            'updated_by_admin_id' => auth('admin')->id(),
        ]);

        return redirect()
            ->route('admin.media-folders.edit', $mediaFolder)
            ->with('success', 'อัปเดตโฟลเดอร์สื่อเรียบร้อยแล้ว');
    }

    public function destroy(MediaFolder $mediaFolder): RedirectResponse
    {
        if ($mediaFolder->children()->exists()) {
            return redirect()
                ->route('admin.media-folders.index')
                ->with('error', 'ไม่สามารถลบโฟลเดอร์นี้ได้ เนื่องจากยังมีโฟลเดอร์ย่อยอยู่');
        }

        if ($mediaFolder->media()->exists()) {
            return redirect()
                ->route('admin.media-folders.index')
                ->with('error', 'ไม่สามารถลบโฟลเดอร์นี้ได้ เนื่องจากยังมีไฟล์สื่ออยู่ในโฟลเดอร์');
        }

        $mediaFolder->delete();

        return redirect()
            ->route('admin.media-folders.index')
            ->with('success', 'ลบโฟลเดอร์สื่อเรียบร้อยแล้ว');
    }
}