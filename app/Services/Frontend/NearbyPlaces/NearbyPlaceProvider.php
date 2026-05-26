<?php

namespace App\Services\Frontend\NearbyPlaces;

use Illuminate\Support\Collection;

interface NearbyPlaceProvider
{
    public function search(float $latitude, float $longitude, string $category, array $settings): Collection;
}
