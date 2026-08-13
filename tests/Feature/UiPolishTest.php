<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiPolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_renders_cleanly_with_rtl_direction()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('dir="rtl"', false);
    }

    public function test_property_listing_page_renders_with_arabic_typography_and_badges()
    {
        $this->seed(\Database\Seeders\DefaultTeamSeeder::class);
        $this->seed(\Database\Seeders\RolesSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\PropertyCategorySeeder::class);
        $this->seed(\Database\Seeders\NeighborhoodSeeder::class);

        Property::create([
            'title' => 'شقة فاخرة للبيع حي السلام بالمكلا',
            'description' => 'شقة ممتازة جاهزة للسكن',
            'location' => 'المكلا - حي السلام',
            'price' => 45000000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area_sqft' => 180,
            'year_built' => 2024,
            'property_type' => 'Apartment',
            'status' => 'For Sale',
            'is_featured' => true,
            'user_id' => 1,
        ]);

        $response = $this->get('/properties');

        $response->assertStatus(200);
        $response->assertSee('شقة فاخرة للبيع حي السلام بالمكلا');
        $response->assertSee('للبيع');
        $response->assertSee('ر.ي');
    }

    public function test_css_app_contains_custom_rtl_and_button_classes()
    {
        $cssContent = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('Cairo', $cssContent);
        $this->assertStringContainsString('Tajawal', $cssContent);
        $this->assertStringContainsString('property-card-hover', $cssContent);
        $this->assertStringContainsString('whatsapp-btn', $cssContent);
    }
}
