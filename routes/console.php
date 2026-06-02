<?php

use App\Jobs\Content\Temple\RefreshTempleNearbyRecommendations;
use App\Models\Content\Temple\Temple;
use App\Services\Frontend\NearbyPlaces\NearbyPlaceRecommendationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('nearby-places:refresh {--temple_id=} {--limit=} {--sync}', function (NearbyPlaceRecommendationService $service) {
    $limit = (int) ($this->option('limit') ?: config('nearby_places.refresh.max_temples_per_run', 50));

    $query = Temple::query()
        ->with('content', 'address')
        ->whereHas('content', fn ($query) => $query->where('status', 'published'))
        ->whereHas('address', fn ($query) => $query->whereNotNull('latitude')->whereNotNull('longitude'));

    if ($templeId = $this->option('temple_id')) {
        $query->whereKey((int) $templeId);
    }

    $temples = $query
        ->leftJoin('temple_stats', 'temple_stats.temple_id', '=', 'temples.id')
        ->orderByDesc('temple_stats.view_count')
        ->orderBy('temples.id')
        ->limit($limit)
        ->get('temples.*');

    foreach ($temples as $temple) {
        if ($this->option('sync')) {
            $count = $service->refreshTemple($temple);
            $this->line('Refreshed nearby places for temple #'.$temple->id.' ('.$count.' categories).');

            continue;
        }

        RefreshTempleNearbyRecommendations::dispatch($temple->id);
        $this->line('Queued nearby refresh for temple #'.$temple->id);
    }

    $this->info(($this->option('sync') ? 'Refreshed ' : 'Queued ').$temples->count().' temple nearby refresh '.($this->option('sync') ? 'runs.' : 'jobs.'));
})->purpose('Queue Google Places nearby recommendation refresh jobs for published temples');
