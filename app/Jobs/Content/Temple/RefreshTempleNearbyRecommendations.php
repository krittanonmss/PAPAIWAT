<?php

namespace App\Jobs\Content\Temple;

use App\Models\Content\Temple\Temple;
use App\Services\Frontend\NearbyPlaces\NearbyPlaceRecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshTempleNearbyRecommendations implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 3600;

    public function __construct(public int $templeId, public ?array $categories = null)
    {
    }

    public function uniqueId(): string
    {
        return $this->templeId.':'.implode(',', $this->categories ?? []);
    }

    public function handle(NearbyPlaceRecommendationService $service): void
    {
        $temple = Temple::query()
            ->with('content', 'address')
            ->find($this->templeId);

        if (! $temple || $temple->content?->status !== 'published') {
            return;
        }

        $service->refreshTemple($temple, $this->categories);
    }
}
