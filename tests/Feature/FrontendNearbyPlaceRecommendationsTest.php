<?php

namespace Tests\Feature;

use App\Jobs\Content\Temple\RefreshTempleNearbyRecommendations;
use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleAddress;
use App\Models\Content\Temple\TempleNearbyRecommendation;
use App\Services\Frontend\NearbyPlaces\NearbyPlaceProvider;
use App\Services\Frontend\NearbyPlaces\NearbyPlaceRecommendationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
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
            'fetched_at' => now(),
            'expires_at' => now()->addDays(10),
            'stale_until' => now()->addDays(30),
        ]);

        $this->get(route('temples.show', $temple))
            ->assertOk()
            ->assertSee('วางแผนต่อหลังไหว้พระ')
            ->assertSee('ร้านอาหารใกล้วัด')
            ->assertSee('ดูบน Google Maps');

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

    public function test_refresh_service_stores_ranked_filtered_provider_results(): void
    {
        $temple = $this->createPublishedTempleWithAddress();

        $this->app->bind(NearbyPlaceProvider::class, fn () => new class implements NearbyPlaceProvider {
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
        ]);
        $this->assertDatabaseMissing('temple_nearby_recommendations', [
            'temple_id' => $temple->id,
            'provider_place_id' => 'places/low-review',
        ]);
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
