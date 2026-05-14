<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Content\Media\Media;
use Database\Seeders\SystemAccessSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class MediaUploadFeatureTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
        $this->seed(SystemAccessSeeder::class);
        $this->withoutMiddleware(AdminAuthenticate::class);

        $admin = Admin::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->actingAs($admin, 'admin');
    }

    public function test_admin_can_upload_multiple_media_files_at_once(): void
    {
        Storage::fake('public');

        $this->post(route('admin.media.store'), [
            'files' => [
                UploadedFile::fake()->create('cover-one.jpg', 128, 'image/jpeg'),
                UploadedFile::fake()->create('cover-two.jpg', 256, 'image/jpeg'),
            ],
            'visibility' => 'public',
        ])->assertRedirect(route('admin.media.index'));

        $this->assertSame(2, Media::query()->count());
        $this->assertDatabaseHas('media', [
            'original_filename' => 'cover-one.jpg',
            'media_type' => 'image',
            'visibility' => 'public',
        ]);
        $this->assertDatabaseHas('media', [
            'original_filename' => 'cover-two.jpg',
            'media_type' => 'image',
            'visibility' => 'public',
        ]);
    }

    public function test_media_upload_rejects_files_larger_than_five_megabytes(): void
    {
        Storage::fake('public');

        $this->from(route('admin.media.create'))
            ->post(route('admin.media.store'), [
                'files' => [
                    UploadedFile::fake()->create('too-large.jpg', 5121, 'image/jpeg'),
                ],
                'visibility' => 'public',
            ])
            ->assertRedirect(route('admin.media.create'))
            ->assertSessionHasErrors('files.0');

        $this->assertSame(0, Media::query()->count());
    }
}
