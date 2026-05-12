<?php

namespace App\Services\Content\Media;

use App\Models\Content\Media\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaVariantService
{
    public function generate(Media $media): void
    {
        if ($media->media_type !== 'image') {
            return;
        }

        if (! $media->path || ! Storage::disk($media->disk)->exists($media->path)) {
            return;
        }

        $sourcePath = Storage::disk($media->disk)->path($media->path);
        $imageInfo = @getimagesize($sourcePath);

        if ($imageInfo === false) {
            return;
        }

        $variants = [
            'thumbnail' => 300,
            'medium' => 800,
            'large' => 1200,
        ];

        foreach ($variants as $variantName => $targetWidth) {
            $this->createVariant($media, $sourcePath, $variantName, $targetWidth);
        }
    }

    private function createVariant(Media $media, string $sourcePath, string $variantName, int $targetWidth): void
    {
        $sourceInfo = @getimagesize($sourcePath);

        if ($sourceInfo === false) {
            return;
        }

        [$sourceWidth, $sourceHeight] = $sourceInfo;

        if ($sourceWidth <= 0 || $sourceHeight <= 0) {
            return;
        }

        if ($targetWidth > $sourceWidth) {
            return;
        }

        $targetHeight = (int) round(($targetWidth / $sourceWidth) * $sourceHeight);

        $sourceImage = $this->makeSourceImage($sourcePath, $media->mime_type);

        if (! $sourceImage) {
            return;
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if (in_array($media->mime_type, ['image/png', 'image/webp'], true)) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
        }

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        $extension = $media->extension ?: 'jpg';
        $directory = $media->directory ?: dirname($media->path);
        $filename = pathinfo($media->filename, PATHINFO_FILENAME)
            . '-' . $variantName
            . '-' . Str::uuid()
            . '.' . $extension;

        $variantPath = trim($directory, '/') . '/' . $filename;
        $absoluteVariantPath = Storage::disk($media->disk)->path($variantPath);

        $this->saveImage($targetImage, $absoluteVariantPath, $media->mime_type);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        $media->variants()->updateOrCreate(
            ['variant_name' => $variantName],
            [
                'disk' => $media->disk,
                'directory' => $directory,
                'filename' => $filename,
                'path' => $variantPath,
                'extension' => $extension,
                'mime_type' => $media->mime_type,
                'file_size' => Storage::disk($media->disk)->exists($variantPath)
                    ? Storage::disk($media->disk)->size($variantPath)
                    : 0,
                'width' => $targetWidth,
                'height' => $targetHeight,
                'processing_status' => 'completed',
                'generated_at' => now(),
            ]
        );
    }

    private function makeSourceImage(string $sourcePath, string $mimeType): mixed
    {
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => null,
        };
    }

    private function saveImage(mixed $image, string $path, string $mimeType): void
    {
        match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagejpeg($image, $path, 85),
            'image/png' => imagepng($image, $path, 8),
            'image/webp' => imagewebp($image, $path, 85),
            default => null,
        };
    }
}
