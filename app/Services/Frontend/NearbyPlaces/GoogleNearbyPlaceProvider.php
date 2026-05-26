<?php

namespace App\Services\Frontend\NearbyPlaces;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleNearbyPlaceProvider implements NearbyPlaceProvider
{
    public function search(float $latitude, float $longitude, string $category, array $settings): Collection
    {
        $apiKey = (string) config('services.google.places_api_key');

        if ($apiKey === '' || ! config('nearby_places.enabled')) {
            return collect();
        }

        if (! $this->consumeDailyBudget()) {
            Log::warning('Google Places nearby refresh skipped because the daily request guard was reached.', [
                'category' => $category,
            ]);

            return collect();
        }

        $types = array_values(array_filter((array) ($settings['google_types'] ?? [])));

        if ($types === []) {
            return collect();
        }

        try {
            $response = Http::timeout(8)
                ->retry(2, 250)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => implode(',', [
                        'places.id',
                        'places.displayName',
                        'places.rating',
                        'places.userRatingCount',
                        'places.location',
                        'places.googleMapsUri',
                        'places.types',
                        'places.primaryType',
                    ]),
                ])
                ->post('https://places.googleapis.com/v1/places:searchNearby', [
                    'includedTypes' => $types,
                    'maxResultCount' => min(20, max((int) ($settings['limit'] ?? 6) * 3, 6)),
                    'rankPreference' => 'POPULARITY',
                    'languageCode' => 'th',
                    'regionCode' => 'TH',
                    'locationRestriction' => [
                        'circle' => [
                            'center' => [
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                            ],
                            'radius' => (float) ($settings['radius_meters'] ?? 3500),
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('Google Places nearby refresh failed.', [
                    'category' => $category,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return collect();
            }

            return collect($response->json('places', []))
                ->map(fn (array $place) => $this->normalize($place, $category))
                ->filter(fn (array $place) => $place['provider_place_id'] !== '' && $place['name'] !== '')
                ->values();
        } catch (\Throwable $exception) {
            Log::warning('Google Places nearby refresh threw an exception.', [
                'category' => $category,
                'message' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    private function normalize(array $place, string $category): array
    {
        return [
            'provider' => 'google',
            'provider_place_id' => (string) ($place['id'] ?? ''),
            'category' => $category,
            'name' => (string) data_get($place, 'displayName.text', ''),
            'rating' => isset($place['rating']) ? (float) $place['rating'] : null,
            'user_ratings_total' => isset($place['userRatingCount']) ? (int) $place['userRatingCount'] : null,
            'latitude' => isset($place['location']['latitude']) ? (float) $place['location']['latitude'] : null,
            'longitude' => isset($place['location']['longitude']) ? (float) $place['location']['longitude'] : null,
            'maps_url' => $place['googleMapsUri'] ?? null,
            'provider_types' => array_values(array_filter((array) ($place['types'] ?? []))),
        ];
    }

    private function consumeDailyBudget(): bool
    {
        $limit = (int) config('nearby_places.cost_guard.daily_request_limit', 0);

        if ($limit <= 0) {
            return true;
        }

        $key = 'nearby_places:google:daily_requests:'.now()->toDateString();
        $count = Cache::increment($key);

        if ($count === 1) {
            Cache::put($key, $count, now()->endOfDay());
        }

        return $count <= $limit;
    }
}
