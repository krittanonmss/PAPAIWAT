<?php

namespace App\Services\Content\Media;

use App\Models\Content\Media\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaVariantService
{
    private const VARIANTS = [
        'thumbnail' => [
            'width' => 300,
            'height' => 300,
            'crop' => true,
        ],
        'medium' => [
            'width' => 800,
            'height' => null,
            'crop' => false,
        ],
        'large' => [
            'width' => 1600,
            'height' => null,
            'crop' => false,
        ],
    ];

    public function generate(Media $media): void
    {
        if ($media->media_type !== 'image') {
            return;
        }

        if (! in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return;
        }

        $disk = $media->disk;
        $sourcePath = $media->path;

        if (! $sourcePath || ! Storage::disk($disk)->exists($sourcePath)) {
            return;
        }

        $absoluteSourcePath = Storage::disk($disk)->path($sourcePath);

        foreach (self::VARIANTS as $variantName => $config) {
            $this->generateVariant($media, $absoluteSourcePath, $variantName, $config);
        }
    }

    private function generateVariant(Media $media, string $absoluteSourcePath, string $variantName, array $config): void
    {
        $sourceImage = $this->createImageResource($absoluteSourcePath, $media->mime_type);

        if (! $sourceImage) {
            return;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        if ($sourceWidth <= 0 || $sourceHeight <= 0) {
            imagedestroy($sourceImage);
            return;
        }

        [$targetWidth, $targetHeight, $srcX, $srcY, $srcWidth, $srcHeight] = $config['crop']
            ? $this->calculateCropSize($sourceWidth, $sourceHeight, $config['width'], $config['height'])
            : $this->calculateResizeSize($sourceWidth, $sourceHeight, $config['width']);

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
            $srcX,
            $srcY,
            $targetWidth,
            $targetHeight,
            $srcWidth,
            $srcHeight
        );

        $directory = trim(($media->directory ?: dirname($media->path)) . '/variants', '/');
        $extension = $media->extension ?: 'jpg';
        $filename = pathinfo($media->filename, PATHINFO_FILENAME)
            . '-' . $variantName
            . '-' . Str::random(8)
            . '.' . $extension;

        $variantPath = $directory . '/' . $filename;
        $absoluteVariantPath = Storage::disk($media->disk)->path($variantPath);

        if (! is_dir(dirname($absoluteVariantPath))) {
            mkdir(dirname($absoluteVariantPath), 0755, true);
        }

        $saved = $this->saveImageResource($targetImage, $absoluteVariantPath, $media->mime_type);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if (! $saved) {
            return;
        }

        $media->variants()->updateOrCreate(
            ['variant_name' => $variantName],
            [
                'disk' => $media->disk,
                'directory' => $directory,
                'filename' => $filename,
                'path' => $variantPath,
                'extension' => $extension,
                'mime_type' => $media->mime_type,
                'file_size' => Storage::disk($media->disk)->size($variantPath),
                'width' => $targetWidth,
                'height' => $targetHeight,
                'processing_status' => 'completed',
                'generated_at' => now(),
            ]
        );
    }

    private function createImageResource(string $path, string $mimeType): mixed
    {
        return match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }

    private function saveImageResource(mixed $image, string $path, string $mimeType): bool
    {
        return match ($mimeType) {
            'image/jpeg' => imagejpeg($image, $path, 85),
            'image/png' => imagepng($image, $path, 6),
            'image/webp' => function_exists('imagewebp') ? imagewebp($image, $path, 85) : false,
            default => false,
        };
    }

    private function calculateResizeSize(int $sourceWidth, int $sourceHeight, int $targetWidth): array
    {
        if ($sourceWidth <= $targetWidth) {
            return [$sourceWidth, $sourceHeight, 0, 0, $sourceWidth, $sourceHeight];
        }

        $targetHeight = (int) round($sourceHeight * ($targetWidth / $sourceWidth));

        return [$targetWidth, $targetHeight, 0, 0, $sourceWidth, $sourceHeight];
    }

    private function calculateCropSize(
        int $sourceWidth,
        int $sourceHeight,
        int $targetWidth,
        int $targetHeight
    ): array {
        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $srcHeight = $sourceHeight;
            $srcWidth = (int) round($sourceHeight * $targetRatio);
            $srcX = (int) round(($sourceWidth - $srcWidth) / 2);
            $srcY = 0;
        } else {
            $srcWidth = $sourceWidth;
            $srcHeight = (int) round($sourceWidth / $targetRatio);
            $srcX = 0;
            $srcY = (int) round(($sourceHeight - $srcHeight) / 2);
        }

        return [$targetWidth, $targetHeight, $srcX, $srcY, $srcWidth, $srcHeight];
    }
}