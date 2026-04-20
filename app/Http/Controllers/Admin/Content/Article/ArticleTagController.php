<?php

namespace App\Http\Controllers\Admin\Content\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Article\StoreArticleTagRequest;
use App\Http\Requests\Admin\Article\UpdateArticleTagRequest;
use App\Models\Content\Article\ArticleTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleTagController extends Controller
{
    public function index(Request $request): View
    {
        $query = ArticleTag::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $articleTags = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.content.article-tags.index', compact('articleTags'));
    }

    public function create(): View
    {
        return view('admin.content.article-tags.create');
    }

    public function store(StoreArticleTagRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        ArticleTag::query()->create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['slug'] ?? $validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.content.article-tags.index')
            ->with('success', 'Article tag created successfully.');
    }

    public function edit(ArticleTag $articleTag): View
    {
        return view('admin.content.article-tags.edit', compact('articleTag'));
    }

    public function update(UpdateArticleTagRequest $request, ArticleTag $articleTag): RedirectResponse
    {
        $validated = $request->validated();

        $articleTag->update([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['slug'] ?? $validated['name'], $articleTag->id),
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.content.article-tags.edit', $articleTag)
            ->with('success', 'Article tag updated successfully.');
    }

    public function destroy(ArticleTag $articleTag): RedirectResponse
    {
        $articleTag->delete();

        return redirect()
            ->route('admin.content.article-tags.index')
            ->with('success', 'Article tag deleted successfully.');
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug !== '' ? $baseSlug : 'tag';
        $counter = 1;

        while (
            ArticleTag::query()
                ->where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}