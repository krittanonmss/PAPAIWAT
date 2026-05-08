<?php

namespace Tests\Feature\Admin;

use App\Models\Admin\Admin;
use App\Models\Content\Temple\Facility;
use App\Services\Admin\Content\Temple\TempleDataSyncService;
use Database\Seeders\SystemAccessSeeder;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class TempleDataSyncServiceTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
    }

    public function test_sync_creates_facilities_inline_and_keeps_one_facility_item_per_facility(): void
    {
        $this->actingAsDefaultAdmin();

        $temple = app(TempleDataSyncService::class)->create([
            'title' => 'วัดทดสอบ Facility',
            'slug' => 'facility-temple',
            'status' => 'draft',
            'facility_items' => [
                ['facility_name' => 'ที่จอดรถ', 'value' => 'มี', 'sort_order' => 0],
                ['facility_name' => 'ห้องน้ำ', 'value' => 'สะอาด', 'sort_order' => 1],
                ['facility_name' => 'ที่จอดรถ', 'value' => 'ข้อมูลซ้ำ', 'sort_order' => 2],
            ],
        ]);

        $this->assertDatabaseHas('facilities', [
            'name' => 'ที่จอดรถ',
            'type_key' => 'temple',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('facilities', [
            'name' => 'ห้องน้ำ',
            'type_key' => 'temple',
            'status' => 'active',
        ]);
        $this->assertSame(2, $temple->facilityItems()->count());
        $this->assertSame(2, Facility::query()->where('type_key', 'temple')->count());
    }

    public function test_sync_opening_hours_supports_all_seven_days_and_deduplicates_old_rows(): void
    {
        $this->actingAsDefaultAdmin();

        $openingHours = [];

        foreach (range(0, 6) as $dayOfWeek) {
            $openingHours[] = [
                'day_of_week' => $dayOfWeek,
                'open_time' => '08:00',
                'close_time' => '17:00',
                'is_closed' => false,
                'note' => 'รอบแรก',
            ];
        }

        $openingHours[] = [
            'day_of_week' => 0,
            'open_time' => '09:00',
            'close_time' => '16:00',
            'is_closed' => false,
            'note' => 'ข้อมูลล่าสุด',
        ];

        $temple = app(TempleDataSyncService::class)->create([
            'title' => 'วัดทดสอบวันทำการ',
            'slug' => 'opening-hours-temple',
            'status' => 'draft',
            'opening_hours' => $openingHours,
        ]);

        $hours = $temple->openingHours()->get();

        $this->assertSame([0, 1, 2, 3, 4, 5, 6], $hours->pluck('day_of_week')->all());
        $this->assertSame('09:00:00', $hours->firstWhere('day_of_week', 0)->open_time->format('H:i:s'));
        $this->assertSame('ข้อมูลล่าสุด', $hours->firstWhere('day_of_week', 0)->note);
        $this->assertSame(7, $hours->count());
    }

    private function actingAsDefaultAdmin(): void
    {
        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin, 'admin');
    }
}
