<?php

namespace Tests\Feature;

use App\Models\Property;
use App\View\Components\PropertyMap;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyMapComponentTest extends TestCase
{
    use RefreshDatabase;

    private function geolocated(array $overrides = []): Property
    {
        return Property::factory()->create($overrides + [
            'latitude' => 14.54,
            'longitude' => 49.13,
            'moderation_status' => 'approved',
        ]);
    }

    public function test_markers_carry_price_currency_and_rental_flag(): void
    {
        // The component used to ignore its input and select no price, so
        // every popup rendered "NaN undefined".
        $rental = $this->geolocated(['status' => 'for_rent', 'price' => 160000, 'currency' => 'YER']);
        $sale = $this->geolocated(['status' => 'for_sale', 'price' => 36000000, 'currency' => 'YER']);
        $this->geolocated(['moderation_status' => 'pending']);
        $this->geolocated(['status' => 'sold']); // no longer available

        $markers = collect((new PropertyMap)->properties)->keyBy('id');

        $this->assertCount(2, $markers);
        $this->assertEquals(160000, $markers[$rental->id]['price']);
        $this->assertSame('YER', $markers[$rental->id]['currency']);
        $this->assertTrue($markers[$rental->id]['rent']);
        $this->assertFalse($markers[$sale->id]['rent']);
    }

    public function test_the_homepage_map_receives_prices(): void
    {
        $this->geolocated(['status' => 'for_rent', 'price' => 160000, 'currency' => 'YER']);

        $this->get('/')
            ->assertOk()
            ->assertSee('"currency":"YER"', false)
            ->assertSee('"rent":true', false);
    }

    public function test_an_empty_marker_list_stays_empty(): void
    {
        $this->geolocated(['status' => 'for_sale']);

        $this->assertSame([], (new PropertyMap([]))->properties);
    }

    public function test_html_in_a_title_is_json_escaped_in_the_marker_data(): void
    {
        $this->geolocated(['status' => 'for_sale', 'title' => '<img src=x onerror=alert(1)>']);

        $this->get('/')->assertOk()->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_popups_are_never_built_from_an_html_string(): void
    {
        // Leaflet renders a string popup as raw HTML; titles are user input,
        // so popups must stay DOM nodes filled via textContent.
        $script = file_get_contents(resource_path('views/components/property-map.blade.php'));

        $this->assertDoesNotMatchRegularExpression('/bindPopup\(\s*[`\'"]/', $script);
        $this->assertStringContainsString('textContent', $script);
    }
}
