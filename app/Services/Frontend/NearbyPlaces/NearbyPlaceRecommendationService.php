<?php

namespace App\Services\Frontend\NearbyPlaces;

use App\Jobs\Content\Temple\RefreshTempleNearbyRecommendations;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleNearbyRecommendation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NearbyPlaceRecommendationService
{
    public function __construct(private readonly NearbyPlaceProvider $provider) {}

    public function forTemple(Temple $temple): Collection
    {
        $items = TempleNearbyRecommendation::query()
            ->displayable()
            ->where('temple_id', $temple->id)
            ->orderBy('category')
            ->orderByDesc('sort_score')
            ->orderBy('distance_meters')
            ->get()
            ->groupBy('category');

        if ($this->shouldRefresh($temple)) {
            $this->queueRefresh($temple);
        }

        return $items;
    }

    public function refreshTemple(Temple $temple, ?array $onlyCategories = null): int
    {
        $temple->loadMissing('address');

        if (! $this->hasCoordinates($temple)) {
            return 0;
        }

        $categories = $this->categories($onlyCategories);
        $refreshed = 0;

        foreach ($categories as $category => $settings) {
            $places = $this->provider->search(
                (float) $temple->address->latitude,
                (float) $temple->address->longitude,
                $category,
                $settings
            );

            if ($places->isEmpty()) {
                continue;
            }

            $this->storeCategory($temple, $category, $settings, $places);
            $refreshed++;
        }

        return $refreshed;
    }

    public function categoryLabels(): array
    {
        return collect(config('nearby_places.categories', []))
            ->mapWithKeys(fn (array $settings, string $category) => [$category => $settings['label'] ?? $category])
            ->all();
    }

    private function storeCategory(Temple $temple, string $category, array $settings, Collection $places): void
    {
        $now = now();
        $expiresAt = $now->copy()->addDays((int) config('nearby_places.cache.ttl_days', 30));
        $staleUntil = $now->copy()->addDays((int) config('nearby_places.cache.stale_ttl_days', 90));
        $limit = (int) ($settings['limit'] ?? 6);
        $seenPlaceIds = [];

        $rankedPlaces = $places
            ->map(function (array $place) use ($temple, $settings): array {
                $place['distance_meters'] = $this->distanceMeters(
                    (float) $temple->address->latitude,
                    (float) $temple->address->longitude,
                    $place['latitude'],
                    $place['longitude']
                );
                $place['sort_score'] = $this->scorePlace($place, $settings);

                return $place;
            })
            ->filter(fn (array $place) => $this->passesFilters($place, $settings, $temple))
            ->sortByDesc('sort_score')
            ->take($limit)
            ->values();

        foreach ($rankedPlaces as $place) {
            $seenPlaceIds[] = $place['provider_place_id'];
            $photoNames = array_slice(array_values(array_filter((array) ($place['photo_names'] ?? []))), 0, 1);

            $recommendation = TempleNearbyRecommendation::query()->updateOrCreate(
                [
                    'temple_id' => $temple->id,
                    'provider' => $place['provider'],
                    'provider_place_id' => $place['provider_place_id'],
                    'category' => $category,
                ],
                [
                    'name' => $place['name'],
                    'rating' => $place['rating'],
                    'user_ratings_total' => $place['user_ratings_total'],
                    'latitude' => $place['latitude'],
                    'longitude' => $place['longitude'],
                    'distance_meters' => $place['distance_meters'],
                    'maps_url' => $place['maps_url'],
                    'sort_score' => $place['sort_score'],
                    'photo_names' => $photoNames,
                    'provider_types' => $place['provider_types'],
                    'fetched_at' => $now,
                    'expires_at' => $expiresAt,
                    'stale_until' => $staleUntil,
                ]
            );

            if ($photoPath = $this->downloadPhoto($recommendation, $photoNames[0] ?? null)) {
                $recommendation->forceFill(['photo_path' => $photoPath])->save();
            }
        }

        if ($seenPlaceIds !== []) {
            TempleNearbyRecommendation::query()
                ->where('temple_id', $temple->id)
                ->where('provider', 'google')
                ->where('category', $category)
                ->whereNotIn('provider_place_id', $seenPlaceIds)
                ->delete();
        }
    }

    private function shouldRefresh(Temple $temple): bool
    {
        if (! config('nearby_places.enabled') || ! config('nearby_places.refresh.lazy')) {
            return false;
        }

        $temple->loadMissing('address');

        if (! $this->hasCoordinates($temple) || $temple->content?->status !== 'published') {
            return false;
        }

        $freshCount = TempleNearbyRecommendation::query()
            ->where('temple_id', $temple->id)
            ->where('expires_at', '>', now())
            ->count();

        if ($freshCount === 0) {
            return true;
        }

        return TempleNearbyRecommendation::query()
            ->where('temple_id', $temple->id)
            ->where('expires_at', '>', now())
            ->whereNotNull('photo_path')
            ->doesntExist();
    }

    private function downloadPhoto(TempleNearbyRecommendation $recommendation, ?string $photoName): ?string
    {
        $apiKey = (string) config('services.google.places_api_key');

        if ($photoName === null || $photoName === '' || $apiKey === '') {
            return null;
        }

        try {
            $response = Http::timeout(12)
                ->retry(2, 250)
                ->withOptions(['allow_redirects' => true])
                ->get('https://places.googleapis.com/v1/'.ltrim($photoName, '/').'/media', [
                    'maxWidthPx' => 640,
                    'key' => $apiKey,
                ]);

            if (! $response->successful()) {
                Log::warning('Google Places photo download failed.', [
                    'nearby_recommendation_id' => $recommendation->id,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $contentType = strtolower((string) $response->header('Content-Type', 'image/jpeg'));

            if (! str_starts_with($contentType, 'image/')) {
                Log::warning('Google Places photo download returned a non-image response.', [
                    'nearby_recommendation_id' => $recommendation->id,
                    'content_type' => $contentType,
                ]);

                return null;
            }

            $extension = str_contains($contentType, 'png') ? 'png' : 'jpg';
            $path = 'nearby-place-recommendations/'.$recommendation->id.'/photo.'.$extension;

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable $exception) {
            Log::warning('Google Places photo download threw an exception.', [
                'nearby_recommendation_id' => $recommendation->id,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function queueRefresh(Temple $temple): void
    {
        $minutes = (int) config('nearby_places.cache.refresh_throttle_minutes', 60);
        $key = 'nearby_places:refresh_queued:temple:'.$temple->id;

        if (! Cache::add($key, true, now()->addMinutes($minutes))) {
            return;
        }

        RefreshTempleNearbyRecommendations::dispatch($temple->id);
    }

    private function categories(?array $onlyCategories): array
    {
        $categories = config('nearby_places.categories', []);

        if ($onlyCategories === null || $onlyCategories === []) {
            return $categories;
        }

        return collect($categories)
            ->only($onlyCategories)
            ->all();
    }

    private function hasCoordinates(Temple $temple): bool
    {
        return $temple->address
            && $temple->address->latitude !== null
            && $temple->address->longitude !== null;
    }

    private function passesFilters(array $place, array $settings, Temple $temple): bool
    {
        if ($place['distance_meters'] === null) {
            return false;
        }

        if ($place['distance_meters'] > (int) ($settings['radius_meters'] ?? 3500)) {
            return false;
        }

        if (($place['rating'] ?? 0) > 0 && $place['rating'] < (float) ($settings['min_rating'] ?? 0)) {
            return false;
        }

        if (($place['user_ratings_total'] ?? 0) > 0 && $place['user_ratings_total'] < (int) ($settings['min_reviews'] ?? 0)) {
            return false;
        }

        $templeTitle = mb_strtolower((string) $temple->content?->title);
        $placeName = mb_strtolower((string) $place['name']);

        return $templeTitle === '' || $placeName === '' || ! str_contains($placeName, $templeTitle);
    }

    private function scorePlace(array $place, array $settings): float
    {
        $rating = (float) ($place['rating'] ?? 0);
        $reviews = (int) ($place['user_ratings_total'] ?? 0);
        $distance = (int) ($place['distance_meters'] ?? ($settings['radius_meters'] ?? 3500));

        $ratingScore = $rating > 0 ? $rating * 20 : 55;
        $reviewScore = min(25, log(max($reviews, 1), 10) * 10);
        $distancePenalty = min(35, ($distance / max((int) ($settings['radius_meters'] ?? 3500), 1)) * 35);

        return round($ratingScore + $reviewScore - $distancePenalty, 2);
    }

    private function distanceMeters(float $fromLat, float $fromLng, ?float $toLat, ?float $toLng): ?int
    {
        if ($toLat === null || $toLng === null) {
            return null;
        }

        $earthRadius = 6371000;
        $latDelta = deg2rad($toLat - $fromLat);
        $lngDelta = deg2rad($toLng - $fromLng);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * sin($lngDelta / 2) ** 2;

        return (int) round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
