<?php

namespace App\Services\Admin\Content\Layout;

use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Models\Content\Layout\Template;
use Illuminate\Support\Facades\DB;

class LayoutVersionService
{
    public function snapshotTemplate(Template $template, string $versionName): void
    {
        $template->versions()->create([
            'version_name' => $versionName,
            'snapshot' => $template->only([
                'name',
                'key',
                'description',
                'view_path',
                'template_type',
                'content_type',
                'schema',
                'status',
                'is_default',
                'sort_order',
            ]),
            'created_by_admin_id' => auth('admin')->id(),
        ]);
    }

    public function snapshotPage(Page $page, string $versionName): void
    {
        $page->refresh();
        $page->loadMissing('sections');

        $page->versions()->create([
            'version_name' => $versionName,
            'snapshot' => [
                'page' => $page->only([
                    'template_id',
                    'title',
                    'slug',
                    'page_type',
                    'status',
                    'is_homepage',
                    'sort_order',
                    'excerpt',
                    'description',
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                    'canonical_url',
                    'og_title',
                    'og_description',
                    'og_image_media_id',
                    'published_at',
                    'unpublished_at',
                ]),
                'sections' => $page->sections
                    ->map(fn (PageSection $section) => $section->only([
                        'id',
                        'name',
                        'section_key',
                        'component_key',
                        'settings',
                        'content',
                        'status',
                        'is_visible',
                        'sort_order',
                    ]))
                    ->values()
                    ->all(),
            ],
            'created_by_admin_id' => auth('admin')->id(),
        ]);
    }

    public function snapshotSection(PageSection $section, string $versionName): void
    {
        $section->versions()->create([
            'version_name' => $versionName,
            'snapshot' => $section->only([
                'page_id',
                'name',
                'section_key',
                'component_key',
                'settings',
                'content',
                'status',
                'is_visible',
                'sort_order',
            ]),
            'created_by_admin_id' => auth('admin')->id(),
        ]);
    }

    public function rollbackPage(Page $page, int $versionId): void
    {
        $version = $page->versions()->findOrFail($versionId);
        $snapshot = $version->snapshot;

        DB::transaction(function () use ($page, $snapshot) {
            $this->snapshotPage($page, 'before_rollback');
            $page->update(array_merge($snapshot['page'] ?? [], [
                'updated_by_admin_id' => auth('admin')->id(),
            ]));

            $page->sections()->delete();

            foreach ($snapshot['sections'] ?? [] as $section) {
                unset($section['id']);
                $page->sections()->create($section);
            }
        });
    }
}
