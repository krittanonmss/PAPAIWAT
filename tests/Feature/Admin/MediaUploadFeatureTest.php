<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\AdminAuthenticate;
use App\Models\Admin\Admin;
use App\Models\Admin\AdminPreference;
use App\Models\Content\Content;
use App\Models\Content\Media\Media;
use App\Models\Content\Media\MediaFolder;
use App\Models\Content\Media\MediaUsage;
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

        $response = $this->post(route('admin.media.store'), [
            'files' => [
                UploadedFile::fake()->image('cover-one.jpg', 100, 100),
                UploadedFile::fake()->image('cover-two.jpg', 120, 120),
            ],
            'visibility' => 'public',
        ]);

        $response->assertRedirect(route('admin.media.index'));

        $this->assertSame(2, Media::query()->count());
        $this->assertDatabaseHas('media', [
            'original_filename' => 'cover-one.jpg',
            'media_type' => 'image',
            'visibility' => 'public',
        ]);
        $this->assertDatabaseHas('media', [
            'original_filename' => 'cover-one.jpg',
            'file_hash' => Media::query()->where('original_filename', 'cover-one.jpg')->value('checksum'),
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

    public function test_media_upload_rejects_unsupported_file_types(): void
    {
        Storage::fake('public');

        $this->from(route('admin.media.create'))
            ->post(route('admin.media.store'), [
                'files' => [
                    UploadedFile::fake()->create('payload.html', 1, 'text/html'),
                ],
                'visibility' => 'public',
            ])
            ->assertRedirect(route('admin.media.create'))
            ->assertSessionHasErrors('files.0');

        $this->assertSame(0, Media::query()->count());
    }

    public function test_media_index_renders_items_as_cards(): void
    {
        Media::query()->create([
            'filename' => 'card-image.jpg',
            'path' => 'uploads/card-image.jpg',
            'original_filename' => 'card-image.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => Admin::query()->first()->id,
            'uploaded_at' => now(),
        ]);

        $this->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee('data-ajax-list-form', false)
            ->assertSee('data-ajax-list-results', false)
            ->assertSee('data-ajax-list-reset', false)
            ->assertSee('<article class="group overflow-hidden rounded-2xl', false)
            ->assertSee('card-image.jpg')
            ->assertDontSee('< class="group', false)
            ->assertDontSee('</>', false);
    }

    public function test_media_index_uses_preferred_view_mode_and_allows_query_override(): void
    {
        $admin = Admin::query()->firstOrFail();

        AdminPreference::query()->create([
            'admin_id' => $admin->id,
            'key' => 'media.default_view_mode',
            'value' => ['value' => 'list'],
        ]);

        Media::query()->create([
            'filename' => 'list-image.jpg',
            'path' => 'uploads/list-image.jpg',
            'original_filename' => 'list-image.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => $admin->id,
            'uploaded_at' => now(),
        ]);

        $this->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee('data-media-view="list"', false)
            ->assertDontSee('data-media-view="grid"', false)
            ->assertSee('list-image.jpg');

        $this->get(route('admin.media.index', ['view_mode' => 'grid']))
            ->assertOk()
            ->assertSee('data-media-view="grid"', false)
            ->assertDontSee('data-media-view="list"', false);
    }

    public function test_media_index_remembers_last_filters_when_preference_is_enabled(): void
    {
        $admin = Admin::query()->firstOrFail();

        AdminPreference::query()->updateOrCreate(
            ['admin_id' => $admin->id, 'key' => 'tables.remember_filters'],
            ['value' => ['value' => true]]
        );

        $this->get(route('admin.media.index', [
            'search' => 'cover',
            'visibility' => 'public',
            'per_page' => 12,
        ]))->assertOk();

        $this->get(route('admin.media.index'))
            ->assertRedirect(route('admin.media.index', [
                'search' => 'cover',
                'visibility' => 'public',
                'per_page' => 12,
            ]));
    }

    public function test_media_index_limits_cards_by_selected_per_page(): void
    {
        foreach (range(1, 15) as $index) {
            Media::query()->create([
                'filename' => "card-image-{$index}.jpg",
                'path' => "uploads/card-image-{$index}.jpg",
                'original_filename' => "card-image-{$index}.jpg",
                'extension' => 'jpg',
                'mime_type' => 'image/jpeg',
                'media_type' => 'image',
                'upload_status' => 'completed',
                'uploaded_by_admin_id' => Admin::query()->first()->id,
                'uploaded_at' => now(),
            ]);
        }

        $this->get(route('admin.media.index', ['per_page' => 12]))
            ->assertOk()
            ->assertSee('แสดง 12 จาก 15 ไฟล์')
            ->assertSee('12 รายการ')
            ->assertSee('card-image-15.jpg')
            ->assertDontSee('card-image-3.jpg');
    }

    public function test_admin_can_bulk_move_selected_media_to_folder(): void
    {
        $admin = Admin::query()->firstOrFail();

        $folder = MediaFolder::query()->create([
            'name' => 'Gallery Set',
            'slug' => 'gallery-set',
            'status' => 'active',
        ]);

        $first = Media::query()->create([
            'filename' => 'bulk-first.jpg',
            'path' => 'uploads/bulk-first.jpg',
            'original_filename' => 'bulk-first.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => $admin->id,
            'uploaded_at' => now(),
        ]);

        $second = Media::query()->create([
            'filename' => 'bulk-second.jpg',
            'path' => 'uploads/bulk-second.jpg',
            'original_filename' => 'bulk-second.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => $admin->id,
            'uploaded_at' => now(),
        ]);

        $this->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee('media-bulk-folder-form', false)
            ->assertSee('ย้ายไฟล์ที่เลือก')
            ->assertSee('เลือกทั้งหมดในหน้านี้');

        $this->patch(route('admin.media.bulk-folder'), [
            'media_ids' => [$first->id, $second->id],
            'media_folder_id' => $folder->id,
        ])->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('media', [
            'id' => $first->id,
            'media_folder_id' => $folder->id,
        ]);
        $this->assertDatabaseHas('media', [
            'id' => $second->id,
            'media_folder_id' => $folder->id,
        ]);

        $this->patch(route('admin.media.bulk-folder'), [
            'media_ids' => [$first->id, $second->id],
            'media_folder_id' => '',
        ])->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('media', [
            'id' => $first->id,
            'media_folder_id' => null,
        ]);
        $this->assertDatabaseHas('media', [
            'id' => $second->id,
            'media_folder_id' => null,
        ]);
    }

    public function test_media_create_and_edit_render_preview_ui(): void
    {
        $media = Media::query()->create([
            'filename' => 'preview-image.jpg',
            'path' => 'uploads/preview-image.jpg',
            'original_filename' => 'preview-image.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => Admin::query()->first()->id,
            'uploaded_at' => now(),
        ]);

        $this->get(route('admin.media.create'))
            ->assertOk()
            ->assertSee('Preview')
            ->assertSee('mediaCreatePreview()', false);

        $this->get(route('admin.media.edit', $media))
            ->assertOk()
            ->assertSee('ตัวอย่างไฟล์')
            ->assertSee('เปลี่ยนไฟล์')
            ->assertSee('mediaEditPreview', false);
    }

    public function test_media_upload_rejects_duplicate_file_hash(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('duplicate.pdf', 12, 'application/pdf');
        $hash = hash_file('sha256', $file->getRealPath());

        Media::query()->create([
            'filename' => 'existing-duplicate.pdf',
            'path' => 'uploads/existing-duplicate.pdf',
            'original_filename' => 'existing-duplicate.pdf',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'media_type' => 'document',
            'checksum' => $hash,
            'file_hash' => $hash,
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => Admin::query()->first()->id,
            'uploaded_at' => now(),
        ]);

        $this->from(route('admin.media.create'))
            ->post(route('admin.media.store'), [
                'files' => [$file],
                'visibility' => 'public',
            ])
            ->assertRedirect(route('admin.media.create'))
            ->assertSessionHasErrors('files');

        $this->assertSame(1, Media::query()->count());
    }

    public function test_media_delete_is_blocked_when_usage_exists_and_edit_shows_usage_viewer(): void
    {
        $media = Media::query()->create([
            'filename' => 'used-media.jpg',
            'path' => 'uploads/used-media.jpg',
            'original_filename' => 'used-media.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'upload_status' => 'completed',
            'uploaded_by_admin_id' => Admin::query()->first()->id,
            'uploaded_at' => now(),
        ]);

        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Uses Media',
            'slug' => 'uses-media',
            'status' => 'draft',
        ]);

        MediaUsage::query()->create([
            'media_id' => $media->id,
            'entity_type' => $content->getMorphClass(),
            'entity_id' => $content->id,
            'role_key' => 'cover',
            'sort_order' => 1,
            'created_by_admin_id' => Admin::query()->first()->id,
        ]);

        $this->get(route('admin.media.edit', $media))
            ->assertOk()
            ->assertSee('ไฟล์นี้ถูกใช้ที่ไหน')
            ->assertSee('role: cover');

        $this->delete(route('admin.media.destroy', $media))
            ->assertRedirect(route('admin.media.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'deleted_at' => null,
        ]);
    }

    public function test_private_media_is_stored_on_local_disk_and_served_through_admin_route(): void
    {
        Storage::fake('local');

        $this->post(route('admin.media.store'), [
            'files' => [
                UploadedFile::fake()->create('private-document.pdf', 8, 'application/pdf'),
            ],
            'visibility' => 'private',
        ])->assertRedirect(route('admin.media.index'));

        $media = Media::query()->where('original_filename', 'private-document.pdf')->firstOrFail();

        $this->assertSame('local', $media->disk);
        $this->assertSame('private', $media->visibility);
        Storage::disk('local')->assertExists($media->path);

        $this->get(route('admin.media.file', $media))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_media_folder_lookup_searches_server_side(): void
    {
        \App\Models\Content\Media\MediaFolder::query()->create([
            'name' => 'Press Kit',
            'slug' => 'press-kit',
            'status' => 'active',
        ]);

        $this->getJson(route('admin.lookups.media-folders', ['q' => 'Press']))
            ->assertOk()
            ->assertJsonFragment([
                'label' => 'Press Kit',
            ]);
    }
}
