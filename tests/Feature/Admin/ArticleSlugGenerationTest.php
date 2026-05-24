<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\Content\Article\ArticleController;
use App\Models\Content\Content;
use App\Support\SlugGenerator;
use ReflectionMethod;
use Tests\TestCase;

class ArticleSlugGenerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ($this->migrationPaths() as $path) {
            $this->artisan('migrate', ['--path' => $path]);
        }
    }

    public function test_generates_stable_romanized_slug_for_thai_title(): void
    {
        $baseSlug = SlugGenerator::make('บทความภาษาไทย', 'article');

        Content::query()->create([
            'content_type' => 'article',
            'title' => 'บทความภาษาไทย',
            'slug' => $baseSlug,
            'status' => 'draft',
        ]);

        $this->assertSame('bthkhwamphasaaithy', $baseSlug);
        $this->assertSame($baseSlug.'-1', $this->generateSlug('บทความภาษาไทย'));
    }

    public function test_generates_unique_slug_against_soft_deleted_content(): void
    {
        $content = Content::query()->create([
            'content_type' => 'article',
            'title' => 'Existing',
            'slug' => 'existing',
            'status' => 'draft',
        ]);

        $content->delete();

        $this->assertSame('existing-1', $this->generateSlug('Existing'));
    }

    private function generateSlug(string $value, ?int $ignoreContentId = null): string
    {
        $method = new ReflectionMethod(ArticleController::class, 'generateUniqueSlug');
        $method->setAccessible(true);

        return $method->invoke(new ArticleController(), $value, $ignoreContentId);
    }

    private function migrationPaths(): array
    {
        return [
            'database/migrations/admin',
            'database/migrations/content',
        ];
    }
}
