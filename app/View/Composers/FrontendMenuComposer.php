<?php

namespace App\View\Composers;

use App\Support\MenuUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class FrontendMenuComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'frontendMenuItems' => $this->getMenuItems('header', true),
            'frontendFooterMenuItems' => $this->getMenuItems('footer'),
        ]);
    }

    private function getMenuItems(?string $locationKey = null, bool $allowFallback = false): Collection
    {
        if (!Schema::hasTable('menus') || !Schema::hasTable('menu_items')) {
            return collect();
        }

        $menu = $this->findMenu($locationKey);

        if (!$menu && $allowFallback) {
            $menu = $this->findMenu();
        }

        if (!$menu) {
            return collect();
        }

        $items = DB::table('menu_items')
            ->where('menu_id', $menu->id)
            ->when(Schema::hasColumn('menu_items', 'deleted_at'), function ($query) {
                $query->whereNull('deleted_at');
            })
            ->when(Schema::hasColumn('menu_items', 'is_enabled'), function ($query) {
                $query->where('is_enabled', true);
            })
            ->when(Schema::hasColumn('menu_items', 'status'), function ($query) {
                $query->where('status', 'active');
            })
            ->when(Schema::hasColumn('menu_items', 'starts_at'), function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                });
            })
            ->when(Schema::hasColumn('menu_items', 'ends_at'), function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                });
            })
            ->when(
                Schema::hasColumn('menu_items', 'sort_order'),
                fn ($query) => $query->orderBy('sort_order'),
                fn ($query) => $query->orderBy('id')
            )
            ->get();

        return $this->buildTree($items);
    }

    private function findMenu(?string $locationKey = null): ?object
    {
        $menu = DB::table('menus')
            ->when(Schema::hasColumn('menus', 'deleted_at'), function ($query) {
                $query->whereNull('deleted_at');
            })
            ->when(Schema::hasColumn('menus', 'status'), function ($query) {
                $query->where('status', 'active');
            })
            ->when($locationKey && Schema::hasColumn('menus', 'location_key'), function ($query) use ($locationKey) {
                $query->where('location_key', $locationKey);
            })
            ->when(!$locationKey && Schema::hasColumn('menus', 'location_key'), function ($query) {
                $query->orderByRaw("case when location_key = 'header' then 0 else 1 end");
            })
            ->when(Schema::hasColumn('menus', 'is_default'), function ($query) {
                $query->orderByDesc('is_default');
            })
            ->when(Schema::hasColumn('menus', 'sort_order'), function ($query) {
                $query->orderBy('sort_order');
            })
            ->orderBy('id')
            ->first();

        return $menu ?: null;
    }

    private function buildTree(Collection $items, ?int $parentId = null): Collection
    {
        return $items
            ->filter(fn ($item) => (int) ($item->parent_id ?? 0) === (int) ($parentId ?? 0))
            ->map(function ($item) use ($items) {
                $item->url = MenuUrl::resolve($item);
                $item->is_active = $this->isActiveUrl($item->url);
                $item->children = $this->buildTree($items, (int) $item->id);
                $item->has_active_child = $item->children->contains(function ($child) {
                    return (bool) ($child->is_active ?? false)
                        || (bool) ($child->has_active_child ?? false);
                });

                return $item;
            })
            ->values();
    }

    private function isActiveUrl(string $url): bool
    {
        if ($url === '#' || str_starts_with($url, '#')) {
            return false;
        }

        return url()->current() === $url
            || request()->fullUrl() === $url
            || request()->is(trim(parse_url($url, PHP_URL_PATH) ?? '', '/') ?: '/');
    }
}
