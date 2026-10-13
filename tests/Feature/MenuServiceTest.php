<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MenuServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_menu_tree_renders_in_order_with_nested_items(): void
    {
        $about = Menu::factory()->create(['name' => 'About', 'url' => '/about', 'order' => 2]);
        Menu::factory()->create(['name' => 'Home', 'url' => '/', 'order' => 1]);
        Menu::factory()->create(['name' => 'Team', 'url' => '/about/team', 'order' => 1, 'parent_id' => $about->id]);

        $html = app(MenuService::class)->buildMenu()->render();

        $this->assertMatchesRegularExpression('#href="/".*href="/about".*href="/about/team"#s', $html);
    }

    public function test_building_the_menu_costs_one_query_however_deep_the_tree(): void
    {
        foreach (range(1, 5) as $i) {
            $parent = Menu::factory()->create(['order' => $i]);
            foreach (range(1, 3) as $j) {
                $child = Menu::factory()->create(['order' => $j, 'parent_id' => $parent->id]);
                Menu::factory()->create(['order' => 1, 'parent_id' => $child->id]);
            }
        }

        DB::enableQueryLog();
        app(MenuService::class)->buildMenu()->render();

        $this->assertCount(1, DB::getQueryLog());
    }

    public function test_soft_deleted_items_are_left_out(): void
    {
        Menu::factory()->create(['name' => 'Visible', 'order' => 1]);
        Menu::factory()->create(['name' => 'Removed', 'order' => 2])->delete();

        $html = app(MenuService::class)->buildMenu()->render();

        $this->assertStringContainsString('Visible', $html);
        $this->assertStringNotContainsString('Removed', $html);
    }
}
