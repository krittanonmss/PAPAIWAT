<?php

namespace App\View\Composers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class FrontendMenuComposer
{
    public function compose(View $view): void
    {
        $view->with('frontendMenuItems', $this->getMenuItems());
    }

    private function getMenuItems(): Collection
    {
        if (!Schema::hasTable('menus') || !Schema::hasTable('menu_items')) {
            return collect();
        }

        $menu = DB::table('menus')
            ->when(Schema::hasColumn('menus', 'status'), function ($query) {
                $query->where('status', 'active');
            })
            ->when(Schema::hasColumn('menus', 'slug'), function ($query) {
                $query->whereIn('slug', [
                    'main',
                    'main-menu',
                    'primary',
                    'primary-menu',
                    'header',
                    'header-menu',
                ]);
            })
            ->orderBy('id')
            ->first();

        if (!$menu) {
            return collect();
        }

        $items = DB::table('menu_items')
            ->where('menu_id', $menu->id)
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

    private function buildTree(Collection $items, ?int $parentId = null): Collection
    {
        return $items
            ->filter(fn ($item) => (int) ($item->parent_id ?? 0) === (int) ($parentId ?? 0))
            ->map(function ($item) use ($items) {
                $item->children = $this->buildTree($items, (int) $item->id);

                return $item;
            })
            ->values();
    }
}