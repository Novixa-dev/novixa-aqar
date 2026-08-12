<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoOpenGraphTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_detail_renders_opengraph_meta_tags_and_json_ld_schema()
    {
        $user = User::factory()->create();
        $property = Property::factory()->create([
            'title' => 'شقة فاخرة للبيع في حي السلام المكلا',
            'description' => 'شقة ممتازة بإطلالة رائعة وقريبة من البحر والمرافق العامة.',
            'price' => 35000000,
            'status' => 'published',
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('property.detail', $property->id));

        $response->assertStatus(200);
        $response->assertSee('og:title', false);
        $response->assertSee('og:description', false);
        $response->assertSee('og:image', false);
        $response->assertSee('twitter:card', false);
        $response->assertSee('RealEstateListing', false);
        $response->assertSee('https://schema.org', false);
    }

    public function test_sitemap_xml_endpoint_returns_valid_xml()
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeaderContains('Content-Type', 'xml');
    }
}
