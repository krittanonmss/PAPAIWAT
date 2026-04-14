<?php

namespace App\Http\Controllers\Admin\Content\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\Media\StoreMediaRequest;
use App\Models\Content\Media\Media;
use App\Models\Content\Media\MediaFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Media::query()
            ->with(['folder', 'uploader'])
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
            if ($request->string('media_folder_id')->toString() === 'none') {
                $query->whereNull('media_folder_id');
            } else {
                $query->where('media_folder_id', (int) $request->input('media_folder_id'));
            }
        }

        $mediaItems = $query->paginate(15)->withQueryString();

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
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $folderOptions = MediaFolder::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $mediaTypes = ['image', 'video', 'audio', 'document', 'other'];

        $mediaByFolderId = $mediaItems->getCollection()
            ->filter(fn ($media) => $media->media_folder_id !== null)
            ->groupBy('media_folder_id');

        $ungroupedMedia = $mediaItems->getCollection()
            ->filter(fn ($media) => $media->media_folder_id === null)
            ->values();

        return view('admin.content.media.items.index', [
            'title' => 'Media Management',
            'mediaItems' => $mediaItems,
            'folders' => $folders,
            'folderOptions' => $folderOptions,
            'mediaTypes' => $mediaTypes,
            'mediaByFolderId' => $mediaByFolderId,
            'ungroupedMedia' => $ungroupedMedia,
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

        Media::query()->create([
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
            'visibility' => $validated['visibility'],
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => auth('admin')->id(),
            'uploaded_at' => now(),
        ]);

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'อัปโหลดไฟล์สำเร็จ');
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

    public function edit(Media $media): View
    {
        $folders = MediaFolder::orderBy('name')->get(['id','name']);

        return view('admin.content.media.items.edit', [
            'title' => 'Edit Media',
            'media' => $media,
            'folders' => $folders,
        ]);
    }

    public function update(Request $request, Media $media): RedirectResponse
    {
        $media->update([
            'title' => $request->input('title'),
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
            'description' => $request->input('description'),
            'media_folder_id' => $request->input('media_folder_id'),
        ]);

        return back()->with('success', 'อัปเดตข้อมูลสำเร็จ');
    }
}