<?php

namespace Tests\Feature;

use App\Jobs\Content\Temple\RefreshTempleNearbyRecommendations;
use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleAddress;
use App\Models\Content\Temple\TempleNearbyRecommendation;
use App\Services\Frontend\NearbyPlaces\GoogleNearbyPlaceProvider;
use App\Services\Frontend\NearbyPlaces\NearbyPlaceProvider;
use App\Services\Frontend\NearbyPlaces\NearbyPlaceRecommendationService;
use App\Support\SiteSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class FrontendNearbyPlaceRecommendationsTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
    }

    public function test_temple_detail_renders_cached_nearby_recommendations_without_refreshing(): void
    {
        Queue::fake();

        $temple = $this->createPublishedTempleWithAddress();

        TempleNearbyRecommendation::query()->create([
            'temple_id' => $temple->id,
            'provider' => 'google',
            'provider_place_id' => 'places/restaurant-a',
            'category' => 'restaurant',
            'name' => 'ร้านอาหารใกล้วัด',
            'rating' => 4.6,
            'user_ratings_total' => 120,
            'distance_meters' => 450,
            'maps_url' => 'https://maps.google.com/?cid=restaurant-a',
            'sort_score' => 92.5,
            'photo_names' => ['places/restaurant-a/photos/photo-1'],
            'photo_path' => 'nearby-place-recommendations/restaurant-a/photo.jpg',
            'fetched_at' => now(),
            'expires_at' => now()->addDays(10),
            'stale_until' => now()->addDays(30),
        ]);

        $this->get(route('temples.show', $temple))
            ->assertOk()
            ->assertSee('วางแผนต่อหลังไหว้พระ')
            ->assertSee('ร้านอาหารใกล้วัด')
            ->assertSee('src="/storage/nearby-place-recommendations/restaurant-a/photo.jpg"', false)
            ->assertSee('เปิดแผนที่');

        Queue::assertNotPushed(RefreshTempleNearbyRecommendations::class);
    }

    public function test_temple_detail_serves_stale_cache_and_queues_background_refresh(): void
    {
        Queue::fake();

        $temple = $this->createPublishedTempleWithAddress();

        TempleNearbyRecommendation::query()->create([
            'temple_id' => $temple->id,
            'provider' => 'google',
            'provider_place_id' => 'places/cafe-a',
            'category' => 'cafe',
            'name' => 'คาเฟ่ใกล้วัด',
            'rating' => 4.4,
            'user_ratings_total' => 80,
            'distance_meters' => 900,
            'maps_url' => 'https://maps.google.com/?cid=cafe-a',
            'sort_score' => 88.0,
            'fetched_at' => now()->subDays(35),
            'expires_at' => now()->subDay(),
            'stale_until' => now()->addDays(30),
        ]);

        $this->get(route('temples.show', $temple))
            ->assertOk()
            ->assertSee('คาเฟ่ใกล้วัด');

        Queue::assertPushed(RefreshTempleNearbyRecommendations::class, fn ($job) => $job->templeId === $temple->id);
    }

    public function test_temple_detail_queues_refresh_when_cached_recommendations_have_no_photos(): void
    {
        Queue::fake();

        $temple = $this->createPublishedTempleWithAddress();

        TempleNearbyRecommendation::query()->create([
            'temple_id' => $temple->id,
            'provider' => 'google',
            'provider_place_id' => 'places/cafe-without-photo-cache',
            'category' => 'cafe',
            'name' => 'คาเฟ่ cache เก่า',
            'rating' => 4.4,
            'user_ratings_total' => 80,
            'distance_meters' => 900,
            'maps_url' => 'https://maps.google.com/?cid=cafe-without-photo-cache',
            'sort_score' => 88.0,
            'fetched_at' => now(),
            'expires_at' => now()->addDays(10),
            'stale_until' => now()->addDays(30),
        ]);

        $this->get(route('temples.show', $temple))
            ->assertOk()
            ->assertSee('คาเฟ่ cache เก่า');

        Queue::assertPushed(RefreshTempleNearbyRecommendations::class, fn ($job) => $job->templeId === $temple->id);
    }

    public function test_temple_detail_queues_refresh_when_cached_recommendations_have_photo_names_but_no_local_photo(): void
    {
        Queue::fake();

        $temple = $this->createPublishedTempleWithAddress();

        TempleNearbyRecommendation::query()->create([
            'temple_id' => $temple->id,
            'provider' => 'google',
            'provider_place_id' => 'places/cafe-without-local-photo',
            'category' => 'cafe',
            'name' => 'คาเฟ่มีชื่อรูปแต่ยังไม่มีไฟล์',
            'rating' => 4.4,
            'user_ratings_total' => 80,
            'distance_meters' => 900,
            'maps_url' => 'https://maps.google.com/?cid=cafe-without-local-photo',
            'sort_score' => 88.0,
            'photo_names' => ['places/cafe-without-local-photo/photos/photo-1'],
            'fetched_at' => now(),
            'expires_at' => now()->addDays(10),
            'stale_until' => now()->addDays(30),
        ]);

        $this->get(route('temples.show', $temple))
            ->assertOk()
            ->assertSee('คาเฟ่มีชื่อรูปแต่ยังไม่มีไฟล์');

        Queue::assertPushed(RefreshTempleNearbyRecommendations::class, fn ($job) => $job->templeId === $temple->id);
    }

    public function test_refresh_service_stores_ranked_filtered_provider_results(): void
    {
        $temple = $this->createPublishedTempleWithAddress();

        $this->app->bind(NearbyPlaceProvider::class, fn () => new class implements NearbyPlaceProvider
        {
            public function search(float $latitude, float $longitude, string $category, array $settings): Collection
            {
                if ($category !== 'restaurant') {
                    return collect();
                }

                return collect([
                    [
                        'provider' => 'google',
                        'provider_place_id' => 'places/good',
                        'category' => $category,
                        'name' => 'ร้านดีใกล้วัด',
                        'rating' => 4.8,
                        'user_ratings_total' => 250,
                        'latitude' => 13.7470,
                        'longitude' => 100.4940,
                        'maps_url' => 'https://maps.google.com/?cid=good',
                        'photo_names' => [
                            'places/good/photos/photo-1',
                            'places/good/photos/photo-2',
                        ],
                        'provider_types' => ['restaurant'],
                    ],
                    [
                        'provider' => 'google',
                        'provider_place_id' => 'places/low-review',
                        'category' => $category,
                        'name' => 'ร้านรีวิวน้อย',
                        'rating' => 4.9,
                        'user_ratings_total' => 1,
                        'latitude' => 13.7480,
                        'longitude' => 100.4940,
                        'maps_url' => 'https://maps.google.com/?cid=low-review',
                        'provider_types' => ['restaurant'],
                    ],
                ]);
            }
        });

        $service = app(NearbyPlaceRecommendationService::class);

        $this->assertSame(1, $service->refreshTemple($temple, ['restaurant']));

        $this->assertDatabaseHas('temple_nearby_recommendations', [
            'temple_id' => $temple->id,
            'provider_place_id' => 'places/good',
            'category' => 'restaurant',
            'name' => 'ร้านดีใกล้วัด',
            'photo_names' => json_encode([
                'places/good/photos/photo-1',
            ]),
        ]);
        $this->assertDatabaseMissing('temple_nearby_recommendations', [
            'temple_id' => $temple->id,
            'provider_place_id' => 'places/low-review',
        ]);
    }

    public function test_refresh_service_downloads_place_photo_to_public_storage(): void
    {
        Storage::fake('public');
        Http::fake([
            'places.googleapis.com/*' => Http::response('fake image bytes', 200, ['Content-Type' => 'image/jpeg']),
        ]);
        config(['services.google.places_api_key' => 'test-key']);

        $temple = $this->createPublishedTempleWithAddress();

        $this->app->bind(NearbyPlaceProvider::class, fn () => new class implements NearbyPlaceProvider
        {
            public function search(float $latitude, float $longitude, string $category, array $settings): Collection
            {
                if ($category !== 'restaurant') {
                    return collect();
                }

                return collect([
                    [
                        'provider' => 'google',
                        'provider_place_id' => 'places/photo-download',
                        'category' => $category,
                        'name' => 'ร้านมีรูป local',
                        'rating' => 4.8,
                        'user_ratings_total' => 250,
                        'latitude' => 13.7470,
                        'longitude' => 100.4940,
                        'maps_url' => 'https://maps.google.com/?cid=photo-download',
                        'photo_names' => ['places/photo-download/photos/photo-1'],
                        'provider_types' => ['restaurant'],
                    ],
                ]);
            }
        });

        app(NearbyPlaceRecommendationService::class)->refreshTemple($temple, ['restaurant']);

        $recommendation = TempleNearbyRecommendation::query()
            ->where('provider_place_id', 'places/photo-download')
            ->firstOrFail();

        $this->assertSame('nearby-place-recommendations/'.$recommendation->id.'/photo.jpg', $recommendation->photo_path);
        Storage::disk('public')->assertExists($recommendation->photo_path);
    }

    public function test_google_nearby_provider_obeys_maps_integration_setting(): void
    {
        Http::fake();
        config(['services.google.places_api_key' => 'test-key']);
        SiteSettings::saveGroup('integrations', [
            'analytics_measurement_id' => null,
            'tag_manager_container_id' => null,
            'maps_enabled' => false,
            'maps_public_browser_key' => null,
        ]);

        $results = app(GoogleNearbyPlaceProvider::class)->search(13.7466, 100.4930, 'restaurant', [
            'google_types' => ['restaurant'],
            'limit' => 6,
            'radius_meters' => 3500,
        ]);

        $this->assertTrue($results->isEmpty());
        Http::assertNothingSent();
    }

    private function createPublishedTempleWithAddress(): Temple
    {
        $content = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'วัดทดสอบ Nearby',
            'slug' => 'temple-nearby-test-'.str()->random(6),
            'status' => 'published',
            'published_at' => now(),
        ]);

        $temple = Temple::query()->create([
            'content_id' => $content->id,
        ]);

        TempleAddress::query()->create([
            'temple_id' => $temple->id,
            'address_line' => 'กรุงเทพมหานคร',
            'province' => 'กรุงเทพมหานคร',
            'latitude' => 13.7466,
            'longitude' => 100.4930,
        ]);

        return $temple->load('content', 'address');
    }
}
