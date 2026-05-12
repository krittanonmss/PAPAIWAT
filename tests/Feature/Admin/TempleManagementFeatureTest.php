<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Temple\Temple;
use Database\Seeders\SystemAccessSeeder;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class TempleManagementFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);

        $this->admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->actingAs($this->admin, 'admin');
    }

    public function test_admin_can_create_temple_with_core_relationships(): void
    {
        $category = $this->createTempleCategory();

        $this->post(route('admin.temples.store'), $this->templePayload([
            'category_ids' => [$category->id],
            'primary_category_id' => $category->id,
        ]))->assertRedirect(route('admin.temples.index'));

        $content = Content::query()->where('slug', 'test-temple')->firstOrFail();
        $temple = Temple::query()->where('content_id', $content->id)->firstOrFail();

        $this->assertSame('temple', $content->content_type);
        $this->assertSame('published', $content->status);
        $this->assertSame($this->admin->id, $content->created_by_admin_id);
        $this->assertDatabaseHas('temple_addresses', [
            'temple_id' => $temple->id,
            'province' => 'เชียงใหม่',
        ]);
        $this->assertDatabaseHas('temple_opening_hours', [
            'temple_id' => $temple->id,
            'day_of_week' => 6,
            'open_time' => '09:00:00',
            'close_time' => '16:00:00',
        ]);
        $this->assertDatabaseHas('facilities', [
            'name' => 'ลานจอดรถ',
            'type_key' => 'temple',
        ]);
        $this->assertDatabaseHas('temple_highlights', [
            'temple_id' => $temple->id,
            'title' => 'พระอุโบสถ',
        ]);
        $this->assertDatabaseHas('categorizables', [
            'category_id' => $category->id,
            'categorizable_type' => 'content',
            'categorizable_id' => $content->id,
            'is_primary' => true,
        ]);
    }

    public function test_admin_can_update_temple_and_replace_opening_hours(): void
    {
        $this->post(route('admin.temples.store'), $this->templePayload())
            ->assertRedirect(route('admin.temples.index'));

        $temple = Temple::query()->with('content')->firstOrFail();

        $this->put(route('admin.temples.update', $temple), $this->templePayload([
            'title' => 'วัดทดสอบที่แก้แล้ว',
            'slug' => 'updated-temple',
            'status' => 'draft',
            'opening_hours' => [
                [
                    'day_of_week' => 0,
                    'open_time' => '10:00',
                    'close_time' => '15:00',
                    'is_closed' => false,
                    'note' => 'วันอาทิตย์เท่านั้น',
                ],
            ],
        ]))->assertRedirect(route('admin.temples.edit', $temple));

        $temple->refresh();
        $content = $temple->content()->firstOrFail();

        $this->assertSame('วัดทดสอบที่แก้แล้ว', $content->title);
        $this->assertSame('updated-temple', $content->slug);
        $this->assertSame('draft', $content->status);
        $this->assertSame(1, $temple->openingHours()->count());
        $this->assertDatabaseHas('temple_opening_hours', [
            'temple_id' => $temple->id,
            'day_of_week' => 0,
            'open_time' => '10:00:00',
        ]);
        $this->assertDatabaseMissing('temple_opening_hours', [
            'temple_id' => $temple->id,
            'day_of_week' => 6,
        ]);
    }

    public function test_admin_can_soft_delete_temple_content(): void
    {
        $this->post(route('admin.temples.store'), $this->templePayload())
            ->assertRedirect(route('admin.temples.index'));

        $temple = Temple::query()->with('content')->firstOrFail();

        $this->delete(route('admin.temples.destroy', $temple))
            ->assertRedirect(route('admin.temples.index'));

        $this->assertSoftDeleted('contents', ['id' => $temple->content_id]);
    }

    public function test_temple_index_shows_five_items_per_page(): void
    {
        foreach (range(1, 6) as $index) {
            $content = Content::query()->create([
                'content_type' => 'temple',
                'title' => 'วัด '.$index,
                'slug' => 'temple-'.$index,
                'status' => 'published',
            ]);

            Temple::query()->create([
                'content_id' => $content->id,
            ]);
        }

        $this->get(route('admin.temples.index'))
            ->assertOk()
            ->assertViewHas('temples', function ($temples) {
                return $temples->perPage() === 5
                    && $temples->count() === 5
                    && $temples->lastPage() === 2;
            });
    }

    private function templePayload(array $overrides = []): array
    {
        return array_replace([
            'title' => 'วัดทดสอบ',
            'slug' => 'test-temple',
            'status' => 'published',
            'excerpt' => 'คำโปรยวัด',
            'description' => '<p>รายละเอียดวัด</p>',
            'temple_type' => 'royal',
            'sect' => 'มหานิกาย',
            'founded_year' => '2450',
            'address' => [
                'address_line' => '1 ถนนทดสอบ',
                'province' => 'เชียงใหม่',
                'district' => 'เมืองเชียงใหม่',
                'subdistrict' => 'ศรีภูมิ',
                'postal_code' => '50000',
            ],
            'opening_hours' => [
                [
                    'day_of_week' => 1,
                    'open_time' => '08:00',
                    'close_time' => '17:00',
                    'is_closed' => false,
                    'note' => 'วันธรรมดา',
                ],
                [
                    'day_of_week' => 6,
                    'open_time' => '09:00',
                    'close_time' => '16:00',
                    'is_closed' => false,
                    'note' => 'วันเสาร์',
                ],
            ],
            'facility_items' => [
                [
                    'facility_name' => 'ลานจอดรถ',
                    'value' => 'มี',
                    'sort_order' => 0,
                ],
            ],
            'highlights' => [
                [
                    'title' => 'พระอุโบสถ',
                    'description' => 'จุดเด่น',
                    'sort_order' => 0,
                ],
            ],
        ], $overrides);
    }

    private function createTempleCategory(): Category
    {
        return Category::query()->create([
            'name' => 'วัดสำคัญ',
            'slug' => 'important-temple',
            'type_key' => 'temple',
            'status' => 'active',
        ]);
    }
}
