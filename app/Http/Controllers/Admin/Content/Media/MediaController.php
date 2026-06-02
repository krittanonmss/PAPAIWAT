<?php

namespace App\Http\Controllers\Admin\Content\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Media\StoreMediaRequest;
use App\Http\Requests\Admin\Content\Media\UpdateMediaRequest;
use App\Jobs\Content\Media\GenerateMediaVariants;
use App\Models\Content\Media\Media;
use App\Models\Content\Media\MediaFolder;
use App\Services\Admin\AdminPreferenceService;
use App\Services\Content\Media\MediaVariantService;
use App\Support\SiteSettings;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;
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

        $preferenceService = app(AdminPreferenceService::class);
        $adminPreferences = $preferenceService->forAdmin($request->user('admin'));

        $perPageOptions = AdminPreferenceService::PER_PAGE_OPTIONS;
        $defaultPerPage = $preferenceService->preferredPerPage($request->user('admin'), $perPageOptions, 24);
        $perPage = $request->integer('per_page', $defaultPerPage);
        $perPage = in_array($perPage, $perPageOptions, true) ? $perPage : $defaultPerPage;

        $defaultViewMode = $adminPreferences['media.default_view_mode'] ?? 'grid';
        $defaultViewMode = in_array($defaultViewMode, ['grid', 'list'], true) ? $defaultViewMode : 'grid';
        $viewMode = $request->string('view_mode')->toString() ?: $defaultViewMode;
        $viewMode = in_array($viewMode, ['grid', 'list'], true) ? $viewMode : $defaultViewMode;

        $mediaItems = $query->paginate($perPage)->withQueryString();

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
            'per_page' => $perPage,
            'view_mode' => $viewMode,
        ];

        $mediaTypes = ['image', 'video', 'audio', 'document', 'other'];

        return view('admin.content.media.items.index', [
            'title' => 'จัดการคลังสื่อ',
            'mediaItems' => $mediaItems,
            'folders' => $folders,
            'selectedFolder' => $selectedFolder,
            'selectedFolderId' => $selectedFolderId,
            'stats' => $stats,
            'filters' => $filters,
            'mediaTypes' => $mediaTypes,
            'perPageOptions' => $perPageOptions,
        ]);
    }

    public function create(): View
    {
        return view('admin.content.media.items.create', [
            'title' => 'อัปโหลดสื่อ',
        ]);
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedFiles = $this->uploadedFiles($request);

        foreach ($uploadedFiles as $uploadedFile) {
            $fileHash = $this->fileHash($uploadedFile);

            if ($this->rejectDuplicates() && $this->duplicateExists($fileHash)) {
                return back()
                    ->withErrors(['files' => 'พบไฟล์ซ้ำในระบบแล้ว กรุณาใช้ไฟล์เดิมจากคลังสื่อ'])
                    ->withInput();
            }

            $media = $this->storeUploadedMedia($uploadedFile, [
                'media_folder_id' => $validated['media_folder_id'] ?? null,
                'title' => count($uploadedFiles) === 1 ? ($validated['title'] ?? null) : null,
                'alt_text' => count($uploadedFiles) === 1 ? ($validated['alt_text'] ?? null) : null,
                'caption' => count($uploadedFiles) === 1 ? ($validated['caption'] ?? null) : null,
                'description' => count($uploadedFiles) === 1 ? ($validated['description'] ?? null) : null,
                'visibility' => $validated['visibility'] ?? SiteSettings::get('media', 'default_visibility', 'public'),
                'file_hash' => $fileHash,
            ]);

            GenerateMediaVariants::dispatch($media->id);
        }

        return redirect()
            ->route('admin.media.index', [
                'media_folder_id' => $validated['media_folder_id'] ?? null,
            ])
            ->with('success', count($uploadedFiles) > 1
                ? 'อัปโหลดไฟล์สำเร็จ ' . count($uploadedFiles) . ' ไฟล์'
                : 'อัปโหลดไฟล์สำเร็จ');
    }

    public function quickUpload(Request $request): RedirectResponse
    {
        $maxKilobytes = max(1024, (int) SiteSettings::get('media', 'max_upload_mb', 5) * 1024);
        $validated = $request->validate([
            'file' => ['nullable', 'image', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'max:'.$maxKilobytes],
            'files' => ['nullable', 'array'],
            'files.*' => ['image', 'mimetypes:image/jpeg,image/png,image/webp,image/gif', 'max:'.$maxKilobytes],
        ]);

        $uploadedFiles = $this->uploadedFiles($request);

        if (count($uploadedFiles) === 0) {
            return back()->withErrors(['file' => 'กรุณาเลือกรูปก่อนอัปโหลด']);
        }

        foreach ($uploadedFiles as $uploadedFile) {
            $fileHash = $this->fileHash($uploadedFile);

            if ($this->rejectDuplicates() && $this->duplicateExists($fileHash)) {
                return back()->withErrors(['file' => 'พบไฟล์ซ้ำในระบบแล้ว กรุณาใช้ไฟล์เดิมจากคลังสื่อ']);
            }

            $media = $this->storeUploadedMedia($uploadedFile, [
                'title' => pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
                'visibility' => SiteSettings::get('media', 'default_visibility', 'public'),
                'file_hash' => $fileHash,
            ]);

            GenerateMediaVariants::dispatch($media->id);
        }

        return back()->with('success', count($uploadedFiles) > 1
            ? 'อัปโหลดรูปใหม่สำเร็จ ' . count($uploadedFiles) . ' รูป'
            : 'อัปโหลดรูปใหม่สำเร็จ');
    }

    public function edit(Media $media): View
    {
        $media->load(['folder', 'variants', 'usages.entity', 'usages.creator', 'tags']);

        return view('admin.content.media.items.edit', [
            'title' => 'แก้ไขสื่อ',
            'media' => $media,
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
            $uploadedFile = $request->file('file');
            $fileHash = $this->fileHash($uploadedFile);

            if ($this->rejectDuplicates() && $this->duplicateExists($fileHash, $media)) {
                return back()
                    ->withErrors(['file' => 'พบไฟล์ซ้ำในระบบแล้ว กรุณาใช้ไฟล์เดิมจากคลังสื่อ'])
                    ->withInput();
            }

            $storedFile = $this->storePhysicalFile($uploadedFile, $validated['visibility']);

            $updateData = array_merge($updateData, [
                'disk' => $storedFile['disk'],
                'directory' => $storedFile['directory'],
                'filename' => $storedFile['filename'],
                'path' => $storedFile['path'],
                'original_filename' => $storedFile['original_filename'],
                'extension' => $storedFile['extension'],
                'mime_type' => $storedFile['mime_type'],
                'media_type' => $storedFile['media_type'],
                'file_size' => $storedFile['file_size'],
                'width' => $storedFile['width'],
                'height' => $storedFile['height'],
                'duration_seconds' => null,
                'checksum' => $fileHash,
                'file_hash' => $fileHash,
                'upload_status' => 'completed',
                'uploaded_by_admin_id' => auth('admin')->id(),
                'uploaded_at' => now(),
            ]);
        }

        $oldFiles = $request->hasFile('file') ? $this->mediaStoragePaths($media) : [];
        $movedFiles = [];

        try {
            if (! $request->hasFile('file') && $media->visibility !== $validated['visibility']) {
                $visibilityMove = $this->moveExistingFilesForVisibility($media, $validated['visibility']);
                $updateData = array_merge($updateData, $visibilityMove['media']);
                $movedFiles = $visibilityMove;
            }

            DB::transaction(function () use ($media, $updateData, $request, $movedFiles): void {
                if ($request->hasFile('file')) {
                    $media->variants()->delete();
                }

                $media->update($updateData);

                foreach ($movedFiles['variants'] ?? [] as $variantId => $variantData) {
                    $media->variants()
                        ->whereKey($variantId)
                        ->update($variantData);
                }
            });

            foreach ($oldFiles as $file) {
                $this->deleteStorageFile($file['disk'], $file['path']);
            }

            foreach ($movedFiles['delete'] ?? [] as $file) {
                $this->deleteStorageFile($file['disk'], $file['path']);
            }
        } catch (Throwable $e) {
            if (isset($storedFile)) {
                $this->deleteStorageFile($storedFile['disk'], $storedFile['path']);
            }

            foreach ($movedFiles['created'] ?? [] as $file) {
                $this->deleteStorageFile($file['disk'], $file['path']);
            }

            throw $e;
        }

        if ($request->hasFile('file')) {
            GenerateMediaVariants::dispatch($media->id);
        }

        return redirect()
            ->route('admin.media.index', [
                'media_folder_id' => $validated['media_folder_id'] ?? null,
            ])
            ->with('success', 'อัปเดตข้อมูลสื่อสำเร็จ');
    }

    public function destroy(Media $media): RedirectResponse
    {
        if ($media->usages()->exists()) {
            return redirect()
                ->route('admin.media.index')
                ->with('error', 'ไม่สามารถลบไฟล์นี้ได้ เนื่องจากยังถูกใช้งานอยู่');
        }

        $files = $this->mediaStoragePaths($media);

        DB::transaction(function () use ($media): void {
            $media->variants()->delete();
            $media->tags()->detach();
            $media->delete();
        });

        foreach ($files as $file) {
            $this->deleteStorageFile($file['disk'], $file['path']);
        }

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'ลบไฟล์สำเร็จ');
    }

    public function bulkUpdateFolder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'media_ids' => ['required', 'array', 'min:1'],
            'media_ids.*' => ['integer', 'exists:media,id'],
            'media_folder_id' => [
                'nullable',
                'integer',
                Rule::exists('media_folders', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
        ], [
            'media_ids.required' => 'กรุณาเลือกไฟล์ที่ต้องการจัดการ',
            'media_ids.min' => 'กรุณาเลือกไฟล์ที่ต้องการจัดการ',
            'media_folder_id.exists' => 'ไม่พบโฟลเดอร์ที่เลือก',
        ]);

        $mediaIds = collect($validated['media_ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $folderId = $validated['media_folder_id'] ?? null;

        $updated = Media::query()
            ->whereIn('id', $mediaIds)
            ->update(['media_folder_id' => $folderId]);

        $folderLabel = $folderId
            ? MediaFolder::query()->whereKey($folderId)->value('name')
            : 'ไม่มีโฟลเดอร์';

        return back()->with('success', "ย้ายไฟล์ {$updated} รายการไปยัง {$folderLabel} แล้ว");
    }

    private function rejectDuplicates(): bool
    {
        return SiteSettings::get('media', 'duplicate_policy', 'reject') === 'reject';
    }

    public function regenerateVariants(Media $media, MediaVariantService $mediaVariantService): RedirectResponse
    {
        if ($media->media_type !== 'image') {
            return back()->with('error', 'ไฟล์นี้ไม่ใช่รูปภาพ จึงไม่สามารถ resize ได้');
        }

        $mediaVariantService->generate($media);

        return back()->with('success', 'สร้าง thumbnail / resize ใหม่สำเร็จ');
    }

    public function file(Media $media): Response|RedirectResponse
    {
        if ($media->visibility !== 'private') {
            return redirect(Storage::disk($media->disk)->url($media->path));
        }

        abort_unless(auth('admin')->user()?->hasPermission('media.view'), 403);
        abort_unless($media->path && Storage::disk($media->disk)->exists($media->path), 404);

        return response(Storage::disk($media->disk)->get($media->path), 200, [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="'.$media->original_filename.'"',
            'Cache-Control' => 'private, no-store',
        ]);
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

    /**
     * @return array<int, UploadedFile>
     */
    private function uploadedFiles(Request $request): array
    {
        if ($request->hasFile('files')) {
            return array_values(array_filter($request->file('files')));
        }

        if ($request->hasFile('file')) {
            return [$request->file('file')];
        }

        return [];
    }

    private function storeUploadedMedia(UploadedFile $uploadedFile, array $metadata = []): Media
    {
        $storedFile = $this->storePhysicalFile($uploadedFile, $metadata['visibility'] ?? 'public');

        try {
            return DB::transaction(fn () => Media::query()->create([
                'media_folder_id' => $metadata['media_folder_id'] ?? null,
                'sort_order' => 0,
                'disk' => $storedFile['disk'],
                'directory' => $storedFile['directory'],
                'filename' => $storedFile['filename'],
                'path' => $storedFile['path'],
                'original_filename' => $storedFile['original_filename'],
                'extension' => $storedFile['extension'],
                'mime_type' => $storedFile['mime_type'],
                'media_type' => $storedFile['media_type'],
                'file_size' => $storedFile['file_size'],
                'width' => $storedFile['width'],
                'height' => $storedFile['height'],
                'duration_seconds' => null,
                'title' => $metadata['title'] ?? null,
                'alt_text' => $metadata['alt_text'] ?? null,
                'caption' => $metadata['caption'] ?? null,
                'description' => $metadata['description'] ?? null,
                'checksum' => $metadata['file_hash'] ?? $this->fileHash($uploadedFile),
                'file_hash' => $metadata['file_hash'] ?? $this->fileHash($uploadedFile),
                'visibility' => $metadata['visibility'] ?? 'public',
                'upload_status' => 'completed',
                'uploaded_by_admin_id' => auth('admin')->id(),
                'uploaded_at' => now(),
            ]));
        } catch (Throwable $e) {
            $this->deleteStorageFile($storedFile['disk'], $storedFile['path']);

            throw $e;
        }
    }

    private function storePhysicalFile(UploadedFile $uploadedFile, string $visibility): array
    {
        $disk = $visibility === 'private' ? 'local' : 'public';
        $folderPath = ($visibility === 'private' ? 'media/private/' : 'media/uploads/') . now()->format('Y/m');
        $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: '');
        $filename = Str::uuid()->toString() . ($extension ? '.' . $extension : '');
        $storedPath = $uploadedFile->storeAs($folderPath, $filename, $disk);

        $mimeType = $this->realMimeType($uploadedFile) ?: 'application/octet-stream';
        $mediaType = $this->resolveMediaType($mimeType);
        $fileSize = $uploadedFile->getSize() ?: 0;
        $width = null;
        $height = null;

        if ($mediaType === 'image') {
            $imageSize = @getimagesize(Storage::disk($disk)->path($storedPath));

            if ($imageSize !== false) {
                $width = isset($imageSize[0]) ? (int) $imageSize[0] : null;
                $height = isset($imageSize[1]) ? (int) $imageSize[1] : null;
            }
        }

        return [
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
        ];
    }

    private function fileHash(UploadedFile $uploadedFile): string
    {
        return hash_file('sha256', $uploadedFile->getRealPath());
    }

    private function duplicateExists(string $fileHash, ?Media $ignore = null): bool
    {
        return Media::withTrashed()
            ->where(function ($query) use ($fileHash) {
                $query->where('file_hash', $fileHash)
                    ->orWhere('checksum', $fileHash);
            })
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->getKey()))
            ->exists();
    }

    private function realMimeType(UploadedFile $uploadedFile): ?string
    {
        return $uploadedFile->getMimeType();
    }

    private function mediaStoragePaths(Media $media): array
    {
        return collect([['disk' => $media->disk, 'path' => $media->path]])
            ->merge($media->variants()->get(['disk', 'path'])->map(fn ($variant) => [
                'disk' => $variant->disk,
                'path' => $variant->path,
            ]))
            ->filter(fn ($file) => filled($file['disk'] ?? null) && filled($file['path'] ?? null))
            ->values()
            ->all();
    }

    private function deleteStorageFile(string $disk, string $path): void
    {
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    private function moveExistingFilesForVisibility(Media $media, string $visibility): array
    {
        $targetDisk = $visibility === 'private' ? 'local' : 'public';

        if ($media->disk === $targetDisk || ! $media->path || ! Storage::disk($media->disk)->exists($media->path)) {
            return ['media' => [], 'variants' => [], 'delete' => [], 'created' => []];
        }

        $moved = [
            'media' => [],
            'variants' => [],
            'delete' => [],
            'created' => [],
        ];

        $mainFile = $this->copyStoredFileToVisibility($media->disk, $media->path, $targetDisk, $visibility);
        $moved['media'] = [
            'disk' => $mainFile['disk'],
            'directory' => $mainFile['directory'],
            'path' => $mainFile['path'],
        ];
        $moved['delete'][] = ['disk' => $media->disk, 'path' => $media->path];
        $moved['created'][] = ['disk' => $mainFile['disk'], 'path' => $mainFile['path']];

        foreach ($media->variants as $variant) {
            if (! $variant->path || ! Storage::disk($variant->disk)->exists($variant->path)) {
                continue;
            }

            $variantFile = $this->copyStoredFileToVisibility($variant->disk, $variant->path, $targetDisk, $visibility);
            $moved['variants'][$variant->id] = [
                'disk' => $variantFile['disk'],
                'directory' => $variantFile['directory'],
                'path' => $variantFile['path'],
            ];
            $moved['delete'][] = ['disk' => $variant->disk, 'path' => $variant->path];
            $moved['created'][] = ['disk' => $variantFile['disk'], 'path' => $variantFile['path']];
        }

        return $moved;
    }

    private function copyStoredFileToVisibility(string $sourceDisk, string $sourcePath, string $targetDisk, string $visibility): array
    {
        $folderPath = ($visibility === 'private' ? 'media/private/' : 'media/uploads/') . now()->format('Y/m');
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $filename = Str::uuid()->toString() . ($extension ? '.' . strtolower($extension) : '');
        $targetPath = $folderPath . '/' . $filename;

        Storage::disk($targetDisk)->put($targetPath, Storage::disk($sourceDisk)->get($sourcePath));

        return [
            'disk' => $targetDisk,
            'directory' => dirname($targetPath),
            'path' => $targetPath,
        ];
    }
}
