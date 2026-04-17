<?php

namespace App\Models\Content\Article;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleStat extends Model
{
    protected $table = 'article_stats';

    public $timestamps = false;

    protected $fillable = [
        'article_id',
        'view_count',
        'like_count',
        'bookmark_count',
        'share_count',
        'updated_at',
    ];

    protected $casts = [
        'article_id' => 'integer',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'bookmark_count' => 'integer',
        'share_count' => 'integer',
        'updated_at' => 'datetime',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}