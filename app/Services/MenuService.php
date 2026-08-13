<?php

namespace App\Services;

use App\Models\Menu;
use Spatie\Menu\Laravel\Menu as SpatieMenu;
use Spatie\Menu\Laravel\Link;

class MenuService
{
    public function buildMenu()
    {
        $menuItems = Menu::whereNull('parent_id')->orderBy('order')->get();

        $menu = SpatieMenu::new()
            ->addClass('flex flex-col lg:flex-row lg:items-center gap-1 lg:gap-6')
            ->addItemClass('block py-2 px-3 lg:p-0 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-200 dark:hover:text-blue-400 rounded-lg lg:rounded-none transition-colors');

        $this->createMenuItems($menuItems)->each(function ($item) use ($menu) {
            $menu->add($item);
        });

        return $menu;
    }

    private function createMenuItems($items)
    {
        return $items->map(function ($item) {
            $displayName = __($item->name);

            if ($item->children && $item->children->count() > 0) {
                $submenu = SpatieMenu::new()
                    ->addClass('absolute start-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl py-2 border border-gray-100 dark:border-gray-700 z-50')
                    ->addItemClass('block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 transition-colors');

                $this->createMenuItems($item->children)->each(function ($subItem) use ($submenu) {
                    $submenu->add($subItem);
                });

                return SpatieMenu::new()
                    ->add(Link::to($item->url, $displayName)->addClass('relative group flex items-center gap-1 font-medium'))
                    ->add($submenu->addClass('hidden group-hover:block'));
            }

            return Link::to($item->url, $displayName);
        });
    }
}

