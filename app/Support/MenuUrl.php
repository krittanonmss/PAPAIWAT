<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class MenuUrl
{
    public static function resolve(object $item): string
    {
        $type = $item->menu_item_type ?? $item->type ?? 'external_url';

        return match ($type) {
            'heading' => '#',
            'route' => self::routeUrl($item),
            'page' => self::pageUrl($item),
            'content' => self::contentUrl($item),
            'anchor' => self::anchorUrl($item),
            'external_url' => self::externalUrl($item),
            default => '#',
        };
    }

    private static function routeUrl(object $item): string
    {
        $routeName = $item->route_name ?? null;

        if (!$routeName || !Route::has($routeName)) {
            return '#';
        }

        $params = [];

        if (!empty($item->route_params)) {
            if (is_array($item->route_params)) {
                $params = $item->route_params;
            } else {
                $decoded = json_decode($item->route_params, true);
                $params = is_array($decoded) ? $decoded : [];
            }
        }

        return route($routeName, $params);
    }

    private static function pageUrl(object $item): string
    {
        if (empty($item->page_id)) {
            return '#';
        }

        $page = DB::table('pages')
            ->where('id', $item->page_id)
            ->first();

        if (!$page || empty($page->slug)) {
            return '#';
        }

        return url('/' . ltrim($page->slug, '/'));
    }

    private static function contentUrl(object $item): string
    {
        if (empty($item->content_id)) {
            return '#';
        }

        $content = DB::table('contents')
            ->where('id', $item->content_id)
            ->first();

        if (!$content || empty($content->slug)) {
            return '#';
        }

        if ($content->content_type === 'article') {
            return route('articles.show', $content->slug);
        }

        if ($content->content_type === 'temple') {
            $temple = DB::table('temples')
                ->where('content_id', $content->id)
                ->first();

            return $temple ? route('temples.show', $temple->id) : '#';
        }

        return url('/' . ltrim($content->slug, '/'));
    }

    private static function anchorUrl(object $item): string
    {
        $anchor = $item->anchor ?? $item->url ?? '#';

        return str_starts_with($anchor, '#') ? $anchor : '#' . $anchor;
    }

    private static function externalUrl(object $item): string
    {
        return $item->external_url
            ?? $item->url
            ?? '#';
    }
}
