<?php

namespace App\Jobs\Content\Media;

use App\Models\Content\Media\Media;
use App\Services\Content\Media\MediaVariantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMediaVariants implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $mediaId)
    {
    }

    public function handle(MediaVariantService $mediaVariantService): void
    {
        $media = Media::query()->find($this->mediaId);

        if (! $media) {
            return;
        }

        $mediaVariantService->generate($media);
    }
}
