<?php

namespace App\Models\Content\Article;

use App\Models\Content\Content;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'content_id',
        'title_en',
        'excerpt_en',
        'body',
        'body_format',
        'author_name',
        'reading_time_minutes',
        'seo_keywords',
        'allow_comments',
        'show_on_homepage',
        'scheduled_at',
        'expired_at',
    ];

    protected $casts = [
        'content_id' => 'integer',
        'reading_time_minutes' => 'integer',
        'allow_comments' => 'boolean',
        'show_on_homepage' => 'boolean',
        'scheduled_at' => 'datetime',
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            ArticleTag::class,
            'article_tag_items',
            'article_id',
            'article_tag_id'
        )->withPivot([
            'created_at',
        ]);
    }

    public function relatedItems(): HasMany
    {
        return $this->hasMany(ArticleRelatedItem::class, 'article_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function relatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'article_related_items',
            'article_id',
            'related_article_id'
        )->withPivot([
            'sort_order',
            'created_at',
        ])->orderByPivot('sort_order')
            ->orderByPivot('created_at');
    }

    public function stat(): HasOne
    {
        return $this->hasOne(ArticleStat::class, 'article_id');
    }
}