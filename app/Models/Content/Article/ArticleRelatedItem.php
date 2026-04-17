<?php

namespace App\Models\Content\Article;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleRelatedItem extends Model
{
    protected $table = 'article_related_items';

    public $timestamps = false;

    protected $fillable = [
        'article_id',
        'related_article_id',
        'sort_order',
        'created_at',
    ];

    protected $casts = [
        'article_id' => 'integer',
        'related_article_id' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function relatedArticle(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'related_article_id');
    }
}