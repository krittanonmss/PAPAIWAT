<?php

namespace Tests\Feature;

use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Models\Content\Temple\Temple;
use Tests\Concerns\MigratesAppDatabase;
use Tests\TestCase;

class FrontendGridSliderSectionTest extends TestCase
{
    use MigratesAppDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateAdminContentTables();
    }

    public function test_article_grid_switches_to_slider_when_items_exceed_threshold(): void
    {
        foreach (range(1, 5) as $index) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => 'บทความ '.$index,
                'slug' => 'slider-article-'.$index,
                'status' => 'published',
                'published_at' => now()->subMinutes($index),
            ]);

            Article::query()->create([
                'content_id' => $content->id,
                'body_format' => 'html',
            ]);
        }

        $page = $this->createPageWithSection('article-grid-slider', 'article_grid', [
            'limit' => 5,
            'slider_threshold' => 4,
            'source' => 'all',
        ]);

        $this->get(route('pages.show', $page->slug))
            ->assertOk()
            ->assertSee('data-section-slider', false)
            ->assertSee('data-section-slider-track', false)
            ->assertSee('data-section-slider-prev', false)
            ->assertSee('data-section-slider-next', false);
    }

    public function test_temple_grid_stays_grid_when_items_do_not_exceed_threshold(): void
    {
        foreach (range(1, 4) as $index) {
            $content = Content::query()->create([
                'content_type' => 'temple',
                'title' => 'วัด '.$index,
                'slug' => 'grid-temple-'.$index,
                'status' => 'published',
            ]);

            Temple::query()->create([
                'content_id' => $content->id,
            ]);
        }

        $page = $this->createPageWithSection('temple-grid-normal', 'temple_grid', [
            'limit' => 4,
            'slider_threshold' => 4,
            'source' => 'all',
        ]);

        $this->get(route('pages.show', $page->slug))
            ->assertOk()
            ->assertDontSee('<div class="relative" data-section-slider>', false)
            ->assertSee('grid gap-6 md:grid-cols-2 xl:grid-cols-4', false);
    }

    public function test_section_design_colors_override_template_text_classes(): void
    {
        $page = $this->createPageWithSection('section-design-colors', 'hero', [
            'background_color' => '#111111',
            'text_color' => '#123456',
            'heading_color' => '#abcdef',
            'muted_text_color' => '#654321',
            'accent_color' => '#fedcba',
            'button_background_color' => '#112233',
            'button_text_color' => '#eeeeee',
            'button_border_color' => '#334455',
            'card_background_color' => '#778899',
            'card_border_color' => '#998877',
            'section_gap' => 'spacious',
            'card_padding' => 'spacious',
            'card_radius' => 'xl',
            'image_aspect_ratio' => 'video',
            'image_radius' => '2xl',
            'hero_overlay_color' => '#010203',
            'hero_overlay_opacity' => 35,
            'hero_content_position' => 'left',
            'hero_vertical_align' => 'bottom',
        ]);

        $this->get(route('pages.show', $page->slug))
            ->assertOk()
            ->assertSee('--section-text-color: #123456', false)
            ->assertSee('--section-heading-color: #abcdef', false)
            ->assertSee('--section-muted-color: #654321', false)
            ->assertSee('--section-accent-color: #fedcba', false)
            ->assertSee('--section-button-bg: #112233', false)
            ->assertSee('--section-button-color: #eeeeee', false)
            ->assertSee('--section-gap: 3rem', false)
            ->assertSee('--section-card-padding: 1.75rem', false)
            ->assertSee('--section-image-aspect: 16 / 9', false)
            ->assertSee('--section-hero-overlay-color: #010203', false)
            ->assertSee('--section-hero-overlay-opacity: 0.35', false)
            ->assertSee('[class~="text-white"], [class*="text-white/"]', false)
            ->assertSee('mr-auto text-left', false)
            ->assertSee('items-end', false);
    }

    private function createPageWithSection(string $slug, string $componentKey, array $settings): Page
    {
        $page = Page::query()->create([
            'title' => $slug,
            'slug' => $slug,
            'status' => 'published',
            'published_at' => now(),
        ]);

        PageSection::query()->create([
            'page_id' => $page->id,
            'name' => $slug,
            'section_key' => $slug,
            'component_key' => $componentKey,
            'content' => [
                'title' => $slug,
            ],
            'settings' => $settings,
            'status' => 'active',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        return $page;
    }
}
