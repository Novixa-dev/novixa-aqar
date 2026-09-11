<?php

namespace Tests\Feature;

use App\Services\AIAssistantService;
use App\Services\AIDescriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The demo runs without an AI subscription, so "no provider configured" is a
 * supported state rather than a fault. These tests pin that down: both
 * services must answer, in the visitor's language, without throwing.
 */
class AiFallbackTest extends TestCase
{
    // The rule-based chatbot the assistant falls back to looks up available
    // properties, so these need a schema even though they assert on text.
    use RefreshDatabase;

    private array $property = [
        'property_type' => 'Villa',
        'bedrooms' => 4,
        'bathrooms' => 3,
        'area_sqft' => 3200,
        'location' => 'Mukalla',
        'price' => 95000000,
    ];

    public function test_description_falls_back_to_local_generation_without_a_key(): void
    {
        Config::set('services.openai.api_key', null);
        Http::fake();

        $description = app(AIDescriptionService::class)
            ->generateDescription($this->property, 'professional');

        $this->assertNotSame('', trim($description));
        $this->assertStringContainsString('Mukalla', $description);

        // No provider configured means no outbound call should be attempted.
        Http::assertNothingSent();
    }

    public function test_description_falls_back_when_the_provider_fails(): void
    {
        Config::set('services.openai.api_key', 'test-key');
        Http::fake(['*' => Http::response(['error' => 'nope'], 401)]);

        $description = app(AIDescriptionService::class)
            ->generateDescription($this->property, 'luxury');

        // A 401 used to throw and take the whole page with it.
        $this->assertNotSame('', trim($description));
        $this->assertStringContainsString('Mukalla', $description);
    }

    public function test_description_uses_the_provider_when_one_answers(): void
    {
        Config::set('services.openai.api_key', 'test-key');
        Http::fake(['*' => Http::response([
            'choices' => [['message' => ['content' => 'A generated description.']]],
        ])]);

        $description = app(AIDescriptionService::class)
            ->generateDescription($this->property, 'professional');

        $this->assertSame('A generated description.', $description);
    }

    public function test_description_is_arabic_when_the_locale_is_arabic(): void
    {
        Config::set('services.openai.api_key', null);
        $this->app->setLocale('ar');

        $description = app(AIDescriptionService::class)
            ->generateDescription($this->property, 'professional');

        $this->assertMatchesRegularExpression('/\p{Arabic}/u', $description);
    }

    public function test_the_assistant_answers_through_the_rule_based_chatbot_without_a_key(): void
    {
        Config::set('services.openai.api_key', null);
        Http::fake();

        $answer = app(AIAssistantService::class)
            ->generateResponse('أبحث عن شقة للإيجار', 'general');

        $this->assertNotSame('', trim($answer));
        $this->assertStringNotContainsString('Failed to generate', $answer);
        Http::assertNothingSent();
    }

    public function test_the_assistant_does_not_throw_when_the_provider_fails(): void
    {
        Config::set('services.openai.api_key', 'test-key');
        Http::fake(['*' => Http::response([], 500)]);

        $answer = app(AIAssistantService::class)
            ->generateResponse('Do you have any apartments?', 'general');

        $this->assertNotSame('', trim($answer));
        $this->assertStringNotContainsString('Failed to generate', $answer);
    }
}
