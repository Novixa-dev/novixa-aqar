<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            [
                'name' => 'الرئيسية',
                'url' => '/',
                'order' => 1
            ],
            [
                'name' => 'العقارات',
                'url' => '/properties',
                'order' => 2,
                'children' => [
                    ['name' => 'للبيع', 'url' => '/properties?status=For+Sale', 'order' => 1],
                    ['name' => 'للايجار', 'url' => '/properties?status=For+Rent', 'order' => 2],
                ]
            ],
            [
                'name' => 'الخدمات',
                'url' => '/services',
                'order' => 3,
                'children' => [
                    ['name' => 'الشراء', 'url' => '/services#buying', 'order' => 1],
                    ['name' => 'البيع', 'url' => '/services#selling', 'order' => 2],
                    ['name' => 'الاستئجار', 'url' => '/services#renting', 'order' => 3],
                ]
            ],
            [
                'name' => 'عن الشركة',
                'url' => '/about',
                'order' => 4
            ],
            [
                'name' => 'اتصل بنا',
                'url' => '/contact',
                'order' => 5
            ],
            [
                'name' => 'الحاسبات',
                'url' => '/calculators',
                'order' => 6
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
        $menu = Menu::firstOrCreate(
            ['name' => $menuData['name'], 'parent_id' => $parentId],
            $menuData
        );

        foreach ($children as $childData) {
            $this->createMenu($childData, $menu->id);
        }
    }
}