<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeBrowseSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_offers_browse_by_type_and_city_with_live_counts(): void
    {
        Property::factory()->create(['property_type' => 'Villa', 'location' => 'المكلا - فوه', 'status' => Property::STATUS_FOR_SALE]);
        Property::factory()->create(['property_type' => 'villa', 'location' => 'المكلا - الديس', 'status' => Property::STATUS_FOR_RENT]);
        Property::factory()->create(['property_type' => 'Apartment', 'location' => 'عدن - كريتر', 'status' => Property::STATUS_FOR_SALE]);
        // Not counted: sold, and pending moderation.
        Property::factory()->create(['property_type' => 'Land', 'location' => 'سيئون - المركز', 'status' => 'sold']);
        Property::factory()->create(['property_type' => 'Land', 'location' => 'سيئون - المركز', 'status' => Property::STATUS_FOR_SALE, 'moderation_status' => 'pending']);

        $response = $this->get('/')->assertOk();

        $types = $response->viewData('browseTypes');
        $this->assertSame(['villa' => 2, 'apartment' => 1], $types->mapWithKeys(fn ($t) => [strtolower($t['value']) => $t['count']])->all());

        $areas = $response->viewData('browseAreas');
        $this->assertSame(['المكلا' => 2, 'عدن' => 1], $areas->mapWithKeys(fn ($a) => [$a['name'] => $a['count']])->all());

        $response->assertSee(route('property.list', ['property_type' => 'Villa']), false)
            ->assertSee(route('property.list', ['search' => 'المكلا']), false)
            ->assertDontSee('سيئون');
    }

    public function test_homepage_links_both_public_request_flows_and_latest_news(): void
    {
        $published = News::factory()->create(['title' => 'Published guide', 'published_at' => now()->subDay()]);
        News::factory()->create(['title' => 'Scheduled guide', 'published_at' => now()->addDay()]);

        $this->get('/')
            ->assertOk()
            ->assertSee(route('property.submit'), false)
            ->assertSee(route('property.request'), false)
            ->assertSee(route('news.detail', $published->slug), false)
            ->assertSee('Published guide')
            ->assertDontSee('Scheduled guide');
    }

    public function test_property_cards_do_not_invent_missing_details(): void
    {
        Property::factory()->create([
            'is_featured' => true,
            'status' => Property::STATUS_FOR_SALE,
            'area_sqft' => 0,
            'bedrooms' => null,
        ]);

        $this->get('/')->assertOk()->assertDontSee('150 م²');
    }
}
