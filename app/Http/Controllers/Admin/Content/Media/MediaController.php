<?php

namespace App\Http\Controllers\Admin\Content\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Media\StoreMediaRequest;
use App\Http\Requests\Admin\Content\Media\UpdateMediaRequest;
use App\Models\Content\Media\Media;
use App\Models\Content\Media\MediaFolder;
use App\Services\Content\Media\MediaVariantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $selectedFolderId = $request->string('media_folder_id')->toString();

        $query = Media::query()
            ->with(['folder', 'uploader', 'variants'])
            ->latest('id');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('original_filename', 'like', '%' . $search . '%')
                    ->orWhere('filename', 'like', '%' . $search . '%')
                    ->orWhere('mime_type', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('media_type')) {
            $query->where('media_type', $request->string('media_type')->toString());
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->string('visibility')->toString());
        }

        if ($request->filled('media_folder_id')) {
            if ($selectedFolderId === 'none') {
                $query->whereNull('media_folder_id');
            } else {
                $query->where('media_folder_id', (int) $selectedFolderId);
            }
        }

        $mediaItems = $query->paginate(24)->withQueryString();

        $folders = MediaFolder::query()
            ->with([
                'children' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('name');
                },
                'children.children' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('name');
                },
                'children.children.children' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('name');
                },
            ])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $folderOptions = MediaFolder::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedFolder = null;

        if ($request->filled('media_folder_id') && $selectedFolderId !== 'none') {
            $selectedFolder = MediaFolder::query()->find((int) $selectedFolderId);
        }

        $stats = [
            'total' => Media::query()->count(),
            'images' => Media::query()->where('media_type', 'image')->count(),
            'documents' => Media::query()->where('media_type', 'document')->count(),
            'unfoldered' => Media::query()->whereNull('media_folder_id')->count(),
        ];

        $filters = [
            'search' => $request->string('search')->toString(),
            'media_type' => $request->string('media_type')->toString(),
            'visibility' => $request->string('visibility')->toString(),
            'media_folder_id' => $selectedFolderId,
        ];

        $mediaTypes = ['image', 'video', 'audio', 'document', 'other'];

        return view('admin.content.media.items.index', [
            'title' => 'Media Management',
            'mediaItems' => $mediaItems,
            'folders' => $folders,
            'folderOptions' => $folderOptions,
            'selectedFolder' => $selectedFolder,
            'selectedFolderId' => $selectedFolderId,
            'stats' => $stats,
            'filters' => $filters,
            'mediaTypes' => $mediaTypes,
        ]);
    }

    public function create(): View
    {
        $folders = MediaFolder::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.content.media.items.create', [
            'title' => 'Upload Media',
            'folders' => $folders,
        ]);
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedFile = $request->file('file');

        $disk = 'public';
        $folderPath = 'media/uploads/' . now()->format('Y/m');
        $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: '');
        $filename = Str::uuid()->toString() . ($extension ? '.' . $extension : '');
        $storedPath = $uploadedFile->storeAs($folderPath, $filename, $disk);

        $mimeType = $uploadedFile->getMimeType() ?: 'application/octet-stream';
        $mediaType = $this->resolveMediaType($mimeType);
        $fileSize = $uploadedFile->getSize() ?: 0;

        $width = null;
        $height = null;

        if ($mediaType === 'image') {
            $absolutePath = Storage::disk($disk)->path($storedPath);
            $imageSize = @getimagesize($absolutePath);

            if ($imageSize !== false) {
                $width = isset($imageSize[0]) ? (int) $imageSize[0] : null;
                $height = isset($imageSize[1]) ? (int) $imageSize[1] : null;
            }
        }

        $media = Media::query()->create([
            'media_folder_id' => $validated['media_folder_id'] ?? null,
            'sort_order' => 0,
            'disk' => $disk,
            'directory' => dirname($storedPath) === '.' ? null : dirname($storedPath),
            'filename' => basename($storedPath),
            'path' => $storedPath,
            'original_filename' => $uploadedFile->getClientOriginalName(),
            'extension' => $extension ?: null,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
            'file_size' => $fileSize,
            'width' => $width,
            'height' => $height,
            'duration_seconds' => null,
            'title' => $validated['title'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'description' => $validated['description'] ?? null,
            'checksum' => hash_file('sha256', $uploadedFile->getRealPath()),
            'visibility' => $validated['visibility'] ?? 'public',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => auth('admin')->id(),
            'uploaded_at' => now(),
        ]);

        app(MediaVariantService::class)->generate($media);

        return redirect()
            ->route('admin.media.index', [
                'media_folder_id' => $validated['media_folder_id'] ?? null,
            ])
            ->with('success', 'อัปโหลดไฟล์สำเร็จ');
    }

    public function quickUpload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'image', 'max:10240'],
        ]);

        $uploadedFile = $validated['file'];

        $disk = 'public';
        $folderPath = 'media/uploads/' . now()->format('Y/m');
        $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: '');
        $filename = Str::uuid()->toString() . ($extension ? '.' . $extension : '');
        $storedPath = $uploadedFile->storeAs($folderPath, $filename, $disk);

        $mimeType = $uploadedFile->getMimeType() ?: 'application/octet-stream';
        $mediaType = $this->resolveMediaType($mimeType);
        $fileSize = $uploadedFile->getSize() ?: 0;

        $width = null;
        $height = null;

        $absolutePath = Storage::disk($disk)->path($storedPath);
        $imageSize = @getimagesize($absolutePath);

        if ($imageSize !== false) {
            $width = isset($imageSize[0]) ? (int) $imageSize[0] : null;
            $height = isset($imageSize[1]) ? (int) $imageSize[1] : null;
        }

        $media = Media::query()->create([
            'media_folder_id' => null,
            'sort_order' => 0,
            'disk' => $disk,
            'directory' => dirname($storedPath) === '.' ? null : dirname($storedPath),
            'filename' => basename($storedPath),
            'path' => $storedPath,
            'original_filename' => $uploadedFile->getClientOriginalName(),
            'extension' => $extension ?: null,
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
            'file_size' => $fileSize,
            'width' => $width,
            'height' => $height,
            'duration_seconds' => null,
            'title' => pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
            'alt_text' => null,
            'caption' => null,
            'description' => null,
            'checksum' => hash_file('sha256', $uploadedFile->getRealPath()),
            'visibility' => 'public',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => auth('admin')->id(),
            'uploaded_at' => now(),
        ]);

        app(MediaVariantService::class)->generate($media);

        return back()->with('success', 'อัปโหลดรูปใหม่สำเร็จ');
    }

    public function edit(Media $media): View
    {
        $media->load(['folder', 'variants', 'usages', 'tags']);

        $folders = MediaFolder::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.content.media.items.edit', [
            'title' => 'Edit Media',
            'media' => $media,
            'folders' => $folders,
        ]);
    }

    public function update(UpdateMediaRequest $request, Media $media): RedirectResponse
    {
        $validated = $request->validated();

        $updateData = [
            'title' => $validated['title'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'description' => $validated['description'] ?? null,
            'media_folder_id' => $validated['media_folder_id'] ?? null,
            'visibility' => $validated['visibility'],
        ];

        if ($request->hasFile('file')) {
            foreach ($media->variants as $variant) {
                if ($variant->path && Storage::disk($variant->disk)->exists($variant->path)) {
                    Storage::disk($variant->disk)->delete($variant->path);
                }
            }

            $media->variants()->delete();

            if ($media->path && Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }

            $uploadedFile = $request->file('file');

            $disk = 'public';
            $folderPath = 'media/uploads/' . now()->format('Y/m');
            $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: '');
            $filename = Str::uuid()->toString() . ($extension ? '.' . $extension : '');
            $storedPath = $uploadedFile->storeAs($folderPath, $filename, $disk);

            $mimeType = $uploadedFile->getMimeType() ?: 'application/octet-stream';
            $mediaType = $this->resolveMediaType($mimeType);
            $fileSize = $uploadedFile->getSize() ?: 0;

            $width = null;
            $height = null;

            if ($mediaType === 'image') {
                $absolutePath = Storage::disk($disk)->path($storedPath);
                $imageSize = @getimagesize($absolutePath);

                if ($imageSize !== false) {
                    $width = isset($imageSize[0]) ? (int) $imageSize[0] : null;
                    $height = isset($imageSize[1]) ? (int) $imageSize[1] : null;
                }
            }

            $updateData = array_merge($updateData, [
                'disk' => $disk,
                'directory' => dirname($storedPath) === '.' ? null : dirname($storedPath),
                'filename' => basename($storedPath),
                'path' => $storedPath,
                'original_filename' => $uploadedFile->getClientOriginalName(),
                'extension' => $extension ?: null,
                'mime_type' => $mimeType,
                'media_type' => $mediaType,
                'file_size' => $fileSize,
                'width' => $width,
                'height' => $height,
                'duration_seconds' => null,
                'checksum' => hash_file('sha256', $uploadedFile->getRealPath()),
                'upload_status' => 'completed',
                'uploaded_by_admin_id' => auth('admin')->id(),
                'uploaded_at' => now(),
            ]);
        }

        $media->update($updateData);

        if ($request->hasFile('file') && $media->media_type === 'image') {
            app(MediaVariantService::class)->generate($media->fresh());
        }

        return back()->with('success', 'อัปเดตข้อมูลสื่อสำเร็จ');
    }

    public function destroy(Media $media): RedirectResponse
    {
        if ($media->usages()->exists()) {
            return redirect()
                ->route('admin.media.index')
                ->with('error', 'ไม่สามารถลบไฟล์นี้ได้ เนื่องจากยังถูกใช้งานอยู่');
        }

        if ($media->variants()->exists()) {
            foreach ($media->variants as $variant) {
                if ($variant->path && Storage::disk($variant->disk)->exists($variant->path)) {
                    Storage::disk($variant->disk)->delete($variant->path);
                }
            }

            $media->variants()->delete();
        }

        if ($media->path && Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }

        $media->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'ลบไฟล์สำเร็จ');
    }

    public function regenerateVariants(Media $media, MediaVariantService $mediaVariantService): RedirectResponse
    {
        if ($media->media_type !== 'image') {
            return back()->with('error', 'ไฟล์นี้ไม่ใช่รูปภาพ จึงไม่สามารถ resize ได้');
        }

        $mediaVariantService->generate($media);

        return back()->with('success', 'สร้าง thumbnail / resize ใหม่สำเร็จ');
    }

    private function resolveMediaType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if (
            str_starts_with($mimeType, 'application/') ||
            str_starts_with($mimeType, 'text/')
        ) {
            return 'document';
        }

        return 'other';
    }
}