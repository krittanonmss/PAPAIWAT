<?php

namespace App\Providers;

use App\Models\Content\Content;
use App\Services\Frontend\NearbyPlaces\GoogleNearbyPlaceProvider;
use App\Services\Frontend\NearbyPlaces\NearbyPlaceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NearbyPlaceProvider::class, GoogleNearbyPlaceProvider::class);
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'content' => Content::class,
        ]);
    }
}
