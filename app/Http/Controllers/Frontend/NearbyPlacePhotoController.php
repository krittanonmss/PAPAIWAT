<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Content\Temple\TempleNearbyRecommendation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class NearbyPlacePhotoController extends Controller
{
    public function show(TempleNearbyRecommendation $recommendation, int $index): Response
    {
        abort_unless($recommendation->stale_until === null || $recommendation->stale_until->isFuture(), 404);

        $photoName = collect($recommendation->photo_names ?? [])->values()->get($index);
        $apiKey = (string) config('services.google.places_api_key');

        abort_if(! is_string($photoName) || $photoName === '' || $apiKey === '', 404);

        $response = Http::timeout(8)
            ->retry(1, 200)
            ->withOptions(['allow_redirects' => true])
            ->get('https://places.googleapis.com/v1/'.ltrim($photoName, '/').'/media', [
                'maxWidthPx' => 640,
                'key' => $apiKey,
            ]);

        abort_unless($response->successful(), 404);

        return response($response->body(), 200)
            ->header('Content-Type', $response->header('Content-Type', 'image/jpeg'))
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
