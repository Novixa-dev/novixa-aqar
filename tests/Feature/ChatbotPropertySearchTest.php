<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Services\ChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotPropertySearchTest extends TestCase
{
    use RefreshDatabase;

    private function listing(array $attributes): Property
    {
        return Property::factory()->create($attributes + ['currency' => 'YER', 'price' => 250000]);
    }

    public function test_it_answers_a_rental_question_with_matching_listings(): void
    {
        app()->setLocale('ar');
        $match = $this->listing(['title' => 'شقة مفروشة في فوه', 'status' => 'for_rent', 'property_type' => 'Apartment', 'location' => 'المكلا - فوه']);
        $this->listing(['title' => 'فيلا للبيع في صنعاء', 'status' => 'for_sale', 'property_type' => 'Villa', 'location' => 'صنعاء - حدة']);

        $reply = app(ChatbotService::class)->processMessage('أبحث عن شقة للإيجار في المكلا')['message'];

        $this->assertStringContainsString('شقة مفروشة في فوه', $reply);
        $this->assertStringContainsString(url('/properties/'.$match->id), $reply);
        $this->assertStringNotContainsString('فيلا للبيع في صنعاء', $reply);
        $this->assertStringNotContainsString('تحديث قوائمنا', $reply);
    }

    public function test_it_relaxes_the_city_before_giving_up(): void
    {
        app()->setLocale('ar');
        $this->listing(['title' => 'شقة في عدن', 'status' => 'for_rent', 'property_type' => 'Apartment', 'location' => 'عدن - كريتر']);

        $reply = app(ChatbotService::class)->processMessage('أريد شقة للإيجار في المكلا')['message'];

        $this->assertStringContainsString('أقرب الخيارات', $reply);
        $this->assertStringContainsString('شقة في عدن', $reply);
    }

    public function test_sold_or_rented_properties_are_never_offered(): void
    {
        app()->setLocale('en');
        $this->listing(['title' => 'Already sold villa', 'status' => 'sold', 'property_type' => 'Villa', 'location' => 'Aden']);

        $reply = app(ChatbotService::class)->processMessage('I want to buy a villa')['message'];

        $this->assertStringNotContainsString('Already sold villa', $reply);
        $this->assertStringContainsString(url('/properties'), $reply);
    }
}
