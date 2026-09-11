<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Writes a marketing description for a property listing.
 *
 * Uses a text-generation provider when one is configured, and composes the
 * description locally when one isn't. The local path is not a degraded
 * error state -- running the demo without an AI subscription is a supported
 * configuration -- so this class never throws for a missing or failing
 * provider. It previously did, and because the caller had no try/catch, a
 * seller clicking "generate description" got a 500 instead of a listing.
 */
class AIDescriptionService
{
    public function generateDescription(array $propertyData, string $tone = 'professional'): string
    {
        $remote = $this->generateRemotely($propertyData, $tone);

        return $remote ?? $this->generateLocally($propertyData, $tone);
    }

    /**
     * Whether a text-generation provider is configured at all.
     */
    public function usesRemoteProvider(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    /**
     * @return string|null the generated text, or null to fall back locally
     */
    private function generateRemotely(array $propertyData, string $tone): ?string
    {
        if (! $this->usesRemoteProvider()) {
            return null;
        }

        try {
            $response = Http::withToken(config('services.openai.api_key'))
                ->timeout((int) config('services.openai.timeout', 20))
                ->asJson()
                ->post(rtrim((string) config('services.openai.base_uri'), '/').'/chat/completions', [
                    'model' => config('services.openai.chat_model'),
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $this->createPrompt($propertyData, $tone)],
                    ],
                    'max_tokens' => 400,
                    'temperature' => 0.7,
                ]);

            if (! $response->successful()) {
                Log::warning('AI description provider returned an error; falling back to local generation.', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $text = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

            return $text !== '' ? $text : null;
        } catch (Throwable $e) {
            Log::warning('AI description provider was unreachable; falling back to local generation.', [
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function systemPrompt(): string
    {
        return app()->getLocale() === 'ar'
            ? 'أنت وكيل عقاري محترف في اليمن تكتب أوصافًا تسويقية للعقارات بالعربية الفصحى المبسطة. اكتب فقرة واحدة موجزة وجذابة.'
            : 'You are a professional real-estate agent writing property descriptions. Write one concise, appealing paragraph.';
    }

    protected function createPrompt(array $propertyData, string $tone): string
    {
        $data = $this->normalise($propertyData);

        if (app()->getLocale() === 'ar') {
            return sprintf(
                'اكتب وصفًا تسويقيًا لعقار من نوع %s، يحتوي على %d غرفة نوم و%d حمام، بمساحة %s، يقع في %s، وسعره %s. %s',
                $data['property_type'],
                $data['bedrooms'],
                $data['bathrooms'],
                $data['area'],
                $data['location'],
                $data['price'],
                $this->getToneInstruction($tone),
            );
        }

        return sprintf(
            'Generate an appealing property description for a %s with %d bedrooms, %d bathrooms, %s, located in %s, priced at %s. %s',
            $data['property_type'],
            $data['bedrooms'],
            $data['bathrooms'],
            $data['area'],
            $data['location'],
            $data['price'],
            $this->getToneInstruction($tone),
        );
    }

    protected function getToneInstruction(string $tone): string
    {
        $arabic = app()->getLocale() === 'ar';

        return match ($tone) {
            'casual' => $arabic
                ? 'استخدم أسلوبًا وديًا وبسيطًا.'
                : 'Use a casual and friendly tone.',
            'luxury' => $arabic
                ? 'استخدم أسلوبًا راقيًا يبرز الفخامة والتميز.'
                : 'Use an upscale and sophisticated tone, emphasizing luxury features.',
            default => $arabic
                ? 'استخدم أسلوبًا مهنيًا ومعلوماتيًا.'
                : 'Use a professional and informative tone.',
        };
    }

    /**
     * Composes the description from the listing's own facts.
     *
     * This is what runs with no provider configured, and it is written to be
     * good enough to publish rather than to be an obvious placeholder: it
     * reads as a listing, in the visitor's language, with no mention of an
     * AI having been unavailable.
     */
    private function generateLocally(array $propertyData, string $tone): string
    {
        $data = $this->normalise($propertyData);

        if (app()->getLocale() === 'ar') {
            $opening = match ($tone) {
                'luxury' => sprintf('%s استثنائي يجمع بين الفخامة والموقع المميز في %s.', $data['property_type'], $data['location']),
                'casual' => sprintf('%s جميل في %s، جاهز لاستقبال سكانه.', $data['property_type'], $data['location']),
                default => sprintf('%s في %s، فرصة عقارية تستحق الاطلاع.', $data['property_type'], $data['location']),
            };

            return trim($opening.' '.sprintf(
                'يضم العقار %d غرفة نوم و%d حمام على مساحة %s، بسعر %s. %s',
                $data['bedrooms'],
                $data['bathrooms'],
                $data['area'],
                $data['price'],
                'للاستفسار أو حجز موعد معاينة، تواصل معنا عبر المنصة.',
            ));
        }

        $opening = match ($tone) {
            'luxury' => sprintf('An exceptional %s combining refined living with a prime position in %s.', $data['property_type'], $data['location']),
            'casual' => sprintf('A lovely %s in %s, ready for its next residents.', $data['property_type'], $data['location']),
            default => sprintf('A well-presented %s in %s, worth a closer look.', $data['property_type'], $data['location']),
        };

        return trim($opening.' '.sprintf(
            'It offers %d bedrooms and %d bathrooms across %s, priced at %s. Get in touch through the platform to ask a question or book a viewing.',
            $data['bedrooms'],
            $data['bathrooms'],
            $data['area'],
            $data['price'],
        ));
    }

    /**
     * @return array{property_type: string, bedrooms: int, bathrooms: int, area: string, location: string, price: string}
     */
    private function normalise(array $propertyData): array
    {
        $arabic = app()->getLocale() === 'ar';

        $area = $propertyData['area_sqft'] ?? null;
        $price = $propertyData['price'] ?? null;

        return [
            'property_type' => (string) ($propertyData['property_type'] ?? ($arabic ? 'عقار' : 'property')),
            'bedrooms' => (int) ($propertyData['bedrooms'] ?? 0),
            'bathrooms' => (int) ($propertyData['bathrooms'] ?? 0),
            'area' => $area === null
                ? ($arabic ? 'مساحة غير محددة' : 'an unspecified area')
                : ($arabic
                    ? number_format((float) $area).' قدم مربع'
                    : number_format((float) $area).' sqft'),
            'location' => (string) ($propertyData['location'] ?? ($arabic ? 'اليمن' : 'Yemen')),
            'price' => $price === null
                ? ($arabic ? 'سعر عند الطلب' : 'a price on application')
                : ($arabic
                    ? number_format((float) $price).' ريال يمني'
                    : number_format((float) $price).' YER'),
        ];
    }
}
