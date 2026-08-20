<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Illuminate\Support\Facades\Schema;

class MenuSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Menu::truncate();
        Schema::enableForeignKeyConstraints();

        $menus = [
            [
                'name' => 'Home',
                'url' => '/',
                'order' => 1
            ],
            [
                'name' => 'Properties',
                'url' => '/properties',
                'order' => 2,
                'children' => [
                    ['name' => 'For Sale', 'url' => '/properties?status=For+Sale', 'order' => 1],
                    ['name' => 'For Rent', 'url' => '/properties?status=For+Rent', 'order' => 2],
                    ['name' => 'List Your Property', 'url' => '/submit-property', 'order' => 3],
                    ['name' => 'Request a Property', 'url' => '/property-request', 'order' => 4],
                ]
            ],
            [
                'name' => 'Services',
                'url' => '/services',
                'order' => 3,
                'children' => [
                    ['name' => 'Property Sales', 'url' => '/services#buying', 'order' => 1],
                    ['name' => 'Property Rental & Leasing', 'url' => '/services#renting', 'order' => 2],
                ]
            ],
            [
                'name' => 'Calculators',
                'url' => '/calculators',
                'order' => 4
            ],
            [
                'name' => 'News',
                'url' => '/news',
                'order' => 5
            ],
            [
                'name' => 'About Us',
                'url' => '/about',
                'order' => 6
            ],
            [
                'name' => 'Contact Us',
                'url' => '/contact',
                'order' => 7
            ],
        ];

        foreach ($menus as $menuData) {
            $this->createMenu($menuData);
        }
    }

    private function createMenu($menuData, $parentId = null)
    {
        $children = $menuData['children'] ?? [];
        unset($menuData['children']);

        $menuData['parent_id'] = $parentId;
        $menu = Menu::create($menuData);

        foreach ($children as $childData) {
            $this->createMenu($childData, $menu->id);
        }
    }
}