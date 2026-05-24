<?php

namespace Tests\Feature;

use App\Models\Content\Article\Article;
use App\Models\Content\Content;
use App\Models\Content\Layout\Page;
use App\Models\Content\Layout\PageSection;
use App\Models\Content\Temple\Temple;
use App\Models\Content\Temple\TempleAddress;
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
            ->assertSee('data-section-card data-section-slider-card', false)
            ->assertSee('data-section-image', false)
            ->assertSee('data-section-items', false)
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
            'show_summary_stats' => true,
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
            ->assertSee('.cms-section-root [data-section-card]', false)
            ->assertSee('.cms-section-root [data-section-button]', false)
            ->assertDontSee('.cms-section-root a[class*="rounded-"]', false)
            ->assertSee('data-section-card data-section-card-padding', false)
            ->assertSee('mr-auto text-left', false)
            ->assertSee('items-end', false);
    }

    public function test_full_list_uses_one_column_setting_for_layout_and_page_size(): void
    {
        foreach (range(1, 3) as $index) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => 'Paged Article '.$index,
                'slug' => 'paged-article-'.$index,
                'status' => 'published',
                'published_at' => now()->addMinutes($index),
            ]);

            Article::query()->create([
                'content_id' => $content->id,
                'body_format' => 'html',
            ]);
        }

        $page = $this->createPageWithSection('full-article-list', 'article_list_full', [
            'list_rows' => 1,
            'list_columns' => 2,
            'grid_columns' => 5,
        ]);

        $this->get(route('pages.show', $page->slug))
            ->assertOk()
            ->assertSee('--section-list-columns: 2', false)
            ->assertSee('section_page_', false)
            ->assertSee('Paged Article 3')
            ->assertSee('Paged Article 2')
            ->assertDontSee('Paged Article 1');
    }

    public function test_full_list_filters_do_not_alter_grid_sections_on_same_page(): void
    {
        foreach (['Match Article', 'Independent Grid Article'] as $index => $title) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => $title,
                'slug' => 'isolated-article-'.$index,
                'status' => 'published',
                'published_at' => now()->addMinutes($index),
            ]);

            Article::query()->create([
                'content_id' => $content->id,
                'body_format' => 'html',
            ]);
        }

        $page = $this->createPageWithSection('filter-isolation', 'article_grid', [
            'source' => 'all',
            'limit' => 4,
        ]);
        $listSection = PageSection::query()->create([
            'page_id' => $page->id,
            'name' => 'Full list',
            'section_key' => 'full-list',
            'component_key' => 'article_list_full',
            'content' => ['title' => 'Full list'],
            'settings' => ['list_rows' => 2, 'list_columns' => 2],
            'status' => 'active',
            'is_visible' => true,
            'sort_order' => 2,
        ]);

        $this->get(route('pages.show', $page->slug).'?section_filters[section_'.$listSection->id.'][search]=Match')
            ->assertOk()
            ->assertSee('Match Article')
            ->assertSee('Independent Grid Article');
    }

    public function test_full_article_list_can_filter_featured_and_popular_collections(): void
    {
        foreach ([
            ['title' => 'Featured Selection', 'featured' => true, 'popular' => false],
            ['title' => 'Popular Selection', 'featured' => false, 'popular' => true],
            ['title' => 'Regular Selection', 'featured' => false, 'popular' => false],
        ] as $index => $item) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => $item['title'],
                'slug' => 'collection-article-'.$index,
                'status' => 'published',
                'is_featured' => $item['featured'],
                'is_popular' => $item['popular'],
                'published_at' => now()->addMinutes($index),
            ]);

            Article::query()->create([
                'content_id' => $content->id,
                'body_format' => 'html',
            ]);
        }

        $page = $this->createPageWithSection('article-collection-filter', 'article_list_full', [
            'source' => 'all',
            'list_rows' => 2,
            'list_columns' => 2,
        ]);
        $section = $page->sections()->firstOrFail();

        $this->get(route('pages.show', $page->slug).'?section_filters[section_'.$section->id.'][collection]=featured')
            ->assertOk()
            ->assertSee('name="section_filters[section_'.$section->id.'][collection]"', false)
            ->assertSee('Featured Selection')
            ->assertDontSee('Popular Selection')
            ->assertDontSee('Regular Selection');

        $this->get(route('pages.show', $page->slug).'?section_filters[section_'.$section->id.'][collection]=popular')
            ->assertOk()
            ->assertSee('Popular Selection')
            ->assertDontSee('Featured Selection')
            ->assertDontSee('Regular Selection');
    }

    public function test_article_grid_all_button_opens_target_list_with_matching_collection_filter(): void
    {
        foreach ([
            ['title' => 'Linked Featured Article', 'featured' => true, 'popular' => false],
            ['title' => 'Linked Popular Article', 'featured' => false, 'popular' => true],
        ] as $index => $item) {
            $content = Content::query()->create([
                'content_type' => 'article',
                'title' => $item['title'],
                'slug' => 'linked-collection-article-'.$index,
                'status' => 'published',
                'is_featured' => $item['featured'],
                'is_popular' => $item['popular'],
                'published_at' => now()->addMinutes($index),
            ]);

            Article::query()->create([
                'content_id' => $content->id,
                'body_format' => 'html',
            ]);
        }

        $targetPage = $this->createPageWithSection('linked-article-list', 'article_list_full', [
            'source' => 'all',
            'list_rows' => 2,
            'list_columns' => 2,
        ]);
        $sourcePage = $this->createPageWithSection('linked-featured-grid', 'article_grid', [
            'source' => 'featured',
            'limit' => 4,
        ]);
        $sourceSection = $sourcePage->sections()->firstOrFail();
        $sourceSection->update([
            'content' => [
                'title' => 'Featured articles',
                'all_button_url' => route('pages.show', $targetPage->slug),
            ],
        ]);

        $filteredUrl = route('pages.show', $targetPage->slug).'?collection=featured';

        $this->get(route('pages.show', $sourcePage->slug))
            ->assertOk()
            ->assertSee('href="'.$filteredUrl.'"', false);

        $this->get($filteredUrl)
            ->assertOk()
            ->assertSee('value="featured" selected', false)
            ->assertSee('Linked Featured Article')
            ->assertDontSee('Linked Popular Article');

        $sourceSection->update(['settings' => ['source' => 'popular', 'limit' => 4]]);
        $popularUrl = route('pages.show', $targetPage->slug).'?collection=popular';

        $this->get(route('pages.show', $sourcePage->slug))
            ->assertOk()
            ->assertSee('href="'.$popularUrl.'"', false);

        $this->get($popularUrl)
            ->assertOk()
            ->assertSee('value="popular" selected', false)
            ->assertSee('Linked Popular Article')
            ->assertDontSee('Linked Featured Article');
    }

    public function test_full_temple_list_filters_temple_type_and_featured_collection(): void
    {
        foreach ([
            ['title' => 'Featured Royal Temple', 'type' => 'พระอารามหลวง', 'featured' => true, 'popular' => false],
            ['title' => 'Popular Royal Temple', 'type' => 'พระอารามหลวง', 'featured' => false, 'popular' => true],
            ['title' => 'Featured Forest Temple', 'type' => 'วัดป่า', 'featured' => true, 'popular' => false],
        ] as $index => $item) {
            $content = Content::query()->create([
                'content_type' => 'temple',
                'title' => $item['title'],
                'slug' => 'filtered-temple-'.$index,
                'status' => 'published',
                'is_featured' => $item['featured'],
                'is_popular' => $item['popular'],
            ]);
            $temple = Temple::query()->create([
                'content_id' => $content->id,
                'temple_type' => $item['type'],
            ]);
            TempleAddress::query()->create([
                'temple_id' => $temple->id,
                'province' => 'เชียงใหม่',
            ]);
        }

        $page = $this->createPageWithSection('temple-filter-list', 'temple_list_full', [
            'source' => 'all',
            'list_rows' => 2,
            'list_columns' => 2,
        ]);
        $section = $page->sections()->firstOrFail();
        $query = '?section_filters[section_'.$section->id.'][temple_type]='.urlencode('พระอารามหลวง')
            .'&section_filters[section_'.$section->id.'][collection]=featured';

        $this->get(route('pages.show', $page->slug).$query)
            ->assertOk()
            ->assertSee('name="section_filters[section_'.$section->id.'][temple_type]"', false)
            ->assertSee('name="section_filters[section_'.$section->id.'][collection]"', false)
            ->assertSee('Featured Royal Temple')
            ->assertDontSee('Popular Royal Temple')
            ->assertDontSee('Featured Forest Temple');
    }

    public function test_temple_grid_all_button_opens_target_list_with_matching_collection_filter(): void
    {
        $content = Content::query()->create([
            'content_type' => 'temple',
            'title' => 'Linked Popular Temple',
            'slug' => 'linked-popular-temple',
            'status' => 'published',
            'is_popular' => true,
        ]);
        Temple::query()->create(['content_id' => $content->id]);

        $targetPage = $this->createPageWithSection('linked-temple-list', 'temple_list_full', [
            'source' => 'all',
            'list_rows' => 2,
            'list_columns' => 2,
        ]);
        $sourcePage = $this->createPageWithSection('linked-popular-temple-grid', 'temple_grid', [
            'source' => 'popular',
            'limit' => 4,
        ]);
        $sourcePage->sections()->firstOrFail()->update([
            'content' => [
                'title' => 'Popular temples',
                'all_button_url' => route('pages.show', $targetPage->slug),
            ],
        ]);
        $filteredUrl = route('pages.show', $targetPage->slug).'?collection=popular';

        $this->get(route('pages.show', $sourcePage->slug))
            ->assertOk()
            ->assertSee('href="'.$filteredUrl.'"', false);

        $this->get($filteredUrl)
            ->assertOk()
            ->assertSee('value="popular" selected', false)
            ->assertSee('Linked Popular Temple');
    }

    public function test_bento_renders_selected_box_size_and_custom_empty_text(): void
    {
        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Large Bento Item',
            'slug' => 'large-bento-item',
            'status' => 'published',
        ]);
        Article::query()->create([
            'content_id' => $content->id,
            'body_format' => 'html',
        ]);

        $page = $this->createPageWithSection('bento-layout', 'travel_discovery_bento', []);
        $section = $page->sections()->firstOrFail();
        $section->update([
            'content' => [
                'title' => 'Bento',
                'bento_slots' => [['content_id' => (string) $content->id, 'size' => 'large']],
            ],
        ]);

        $this->get(route('pages.show', $page->slug))
            ->assertOk()
            ->assertSee('Large Bento Item')
            ->assertSee('lg:col-span-2 lg:row-span-2', false);

        $section->update(['content' => ['title' => 'Bento', 'empty_text' => 'ไม่มีรายการที่เลือกไว้']]);

        $this->get(route('pages.show', $page->slug))
            ->assertOk()
            ->assertSee('ไม่มีรายการที่เลือกไว้');
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
