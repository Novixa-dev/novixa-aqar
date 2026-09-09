<?php

namespace App\Services;

use App\Models\Property;
use App\Models\News;

class ChatbotService
{
    /**
     * Process user message and generate response.
     */
    public function processMessage(string $message): array
    {
        $message = trim($message);

        // Detect intent and generate response
        $intent = $this->detectIntent($message);
        $response = $this->generateResponse($intent, $message);

        return [
            'message' => $response,
            'intent' => $intent,
            'confidence' => $this->calculateConfidence($intent, $message),
        ];
    }

    /**
     * Detect user intent from message (matches English and Arabic keywords).
     */
    private function detectIntent(string $message): string
    {
        // Property search related
        if (preg_match('/\b(looking for|find|search|buy|rent|property|house|apartment|properties)\b/iu', $message)
            || preg_match('/(أبحث|بحث|عقار|عقارات|شقة|منزل|بيت|فيلا|أرض|للبيع|للإيجار|للايجار)/u', $message)) {
            return 'property_search';
        }

        // Price inquiries
        if (preg_match('/\b(price|cost|how much|pricing|budget)\b/iu', $message)
            || preg_match('/(سعر|السعر|تكلفة|التكلفة|كم يكلف|الميزانية|ثمن)/u', $message)) {
            return 'price_inquiry';
        }

        // Booking/viewing
        if (preg_match('/\b(book|schedule|viewing|visit|appointment|tour)\b/iu', $message)
            || preg_match('/(حجز|موعد|معاينة|زيارة|جدولة)/u', $message)) {
            return 'booking_inquiry';
        }

        // Contact/support
        if (preg_match('/\b(contact|speak|talk|agent|help|support)\b/iu', $message)
            || preg_match('/(تواصل|اتصال|وكيل|مساعدة|دعم)/u', $message)) {
            return 'contact_request';
        }

        // Greetings
        if (preg_match('/\b(hello|hi|hey|good morning|good afternoon|good evening)\b/iu', $message)
            || preg_match('/(مرحبا|أهلا|اهلا|السلام عليكم|صباح الخير|مساء الخير)/u', $message)) {
            return 'greeting';
        }

        // Location inquiries
        if (preg_match('/\b(where|location|area|neighborhood|address)\b/iu', $message)
            || preg_match('/(أين|موقع|منطقة|حي|عنوان)/u', $message)) {
            return 'location_inquiry';
        }

        // Features inquiries
        if (preg_match('/\b(bedroom|bathroom|parking|garden|amenities|features)\b/iu', $message)
            || preg_match('/(غرفة نوم|غرف نوم|حمام|موقف|حديقة|مميزات)/u', $message)) {
            return 'features_inquiry';
        }

        return 'general_inquiry';
    }

    /**
     * Generate response based on intent.
     */
    private function generateResponse(string $intent, string $message): string
    {
        $ar = app()->getLocale() === 'ar';

        switch ($intent) {
            case 'greeting':
                return $ar
                    ? 'مرحباً بك في منصتنا العقارية! أنا هنا لمساعدتك في إيجاد العقار المثالي. كيف يمكنني مساعدتك اليوم؟'
                    : "Hello! Welcome to our real estate platform. I'm here to help you find your perfect property. How can I assist you today?";

            case 'property_search':
                $properties = Property::where('status', 'available')->take(3)->get();
                if ($properties->isEmpty()) {
                    return $ar
                        ? 'يسعدني مساعدتك في إيجاد عقار! نقوم حالياً بتحديث قوائمنا. يرجى المحاولة لاحقاً أو التواصل مع فريقنا لمعرفة أحدث العقارات المتاحة.'
                        : "I'd be happy to help you find properties! Currently, we're updating our listings. Please check back soon or contact our team for the latest available properties.";
                }

                $response = $ar
                    ? "يسعدني مساعدتك في إيجاد عقار! إليك بعض أحدث قوائمنا:\n\n"
                    : "I can help you find properties! Here are some of our latest listings:\n\n";
                foreach ($properties as $property) {
                    $response .= "• {$property->title} - ".app(\App\Services\CurrencyService::class)->format($property->price, $property->currency)."\n";
                }
                $response .= $ar
                    ? "\nهل تود معرفة المزيد عن أي منها، أم تريد مني مساعدتك بمتطلبات محددة؟"
                    : "\nWould you like to know more about any of these, or shall I help you with specific requirements?";

                return $response;

            case 'price_inquiry':
                return $ar
                    ? 'تتنوع أسعار عقاراتنا لتناسب مختلف الميزانيات. هل يمكنك إخباري بنطاق ميزانيتك أو نوع العقار الذي تهتم به؟ سيساعدني ذلك في تقديم معلومات أدق.'
                    : "Our properties range from various price points to suit different budgets. Could you tell me your budget range or the type of property you're interested in? This will help me provide more accurate information.";

            case 'booking_inquiry':
                return $ar
                    ? "يسعدني مساعدتك في حجز موعد معاينة! لحجز موعد:\n1. تصفح عقاراتنا المتاحة\n2. اضغط على العقار الذي يهمك\n3. استخدم زر \"حجز معاينة\"\n\nأو هل تود مني توصيلك بأحد وكلائنا لترتيب ذلك؟"
                    : "I can help you schedule a property viewing! To book a viewing, please:\n1. Browse our available properties\n2. Click on the property you're interested in\n3. Use the 'Book a Viewing' button\n\nAlternatively, would you like me to connect you with one of our agents who can arrange this for you?";

            case 'contact_request':
                return $ar
                    ? "يسعدني توصيلك بأحد وكلائنا! يمكنك:\n• الضغط على زر \"تحويل إلى موظف\" للمساعدة الفورية\n• الاتصال بمكتبنا\n• تعبئة نموذج التواصل\n\nهل تود مني تحويل هذه المحادثة إلى موظف مباشرة الآن؟"
                    : "I'd be happy to connect you with one of our agents! You can:\n• Click the 'Escalate to Agent' button for immediate assistance\n• Call us at our office\n• Fill out our contact form\n\nWould you like me to escalate this conversation to a live agent now?";

            case 'location_inquiry':
                return $ar
                    ? 'لدينا عقارات في مواقع متعددة. هل يمكنك إخباري عن المنطقة أو الحي الذي يهمك؟ سيساعدني ذلك في إيجاد أنسب العقارات لك.'
                    : "We have properties in various locations. Could you tell me which area or neighborhood you're interested in? This will help me find the most suitable properties for you.";

            case 'features_inquiry':
                return $ar
                    ? "يسعدني مساعدتك في إيجاد عقار بمواصفات محددة! أخبرني عما تبحث عنه، مثل:\n• عدد غرف النوم\n• الحمامات\n• مواقف السيارات\n• الحديقة\n• مميزات أخرى\n\nما هي المميزات المهمة بالنسبة لك؟"
                    : "I can help you find properties with specific features! Please let me know what you're looking for, such as:\n• Number of bedrooms\n• Bathrooms\n• Parking spaces\n• Garden\n• Other amenities\n\nWhat features are important to you?";

            case 'general_inquiry':
            default:
                return $ar
                    ? "أنا هنا للمساعدة! يمكنني مساعدتك في:\n• إيجاد عقارات للبيع أو الإيجار\n• حجز مواعيد المعاينة\n• الإجابة عن أسئلتك بخصوص خدماتنا\n• توصيلك بوكلائنا\n\nما الذي تود معرفة المزيد عنه؟"
                    : "I'm here to help! I can assist you with:\n• Finding properties for sale or rent\n• Scheduling viewings\n• Answering questions about our services\n• Connecting you with our agents\n\nWhat would you like to know more about?";
        }
    }

    /**
     * Calculate confidence score for intent detection.
     */
    private function calculateConfidence(string $intent, string $message): float
    {
        // Simple confidence calculation based on keyword matches
        $keywords = $this->getIntentKeywords($intent);
        $matches = 0;

        foreach ($keywords as $keyword) {
            if (mb_stripos($message, $keyword) !== false) {
                $matches++;
            }
        }

        return min(1.0, ($matches / max(1, count($keywords))) + 0.3);
    }

    /**
     * Get keywords for intent (English and Arabic).
     */
    private function getIntentKeywords(string $intent): array
    {
        return match ($intent) {
            'greeting' => ['hello', 'hi', 'hey', 'مرحبا', 'أهلا', 'السلام عليكم'],
            'property_search' => ['property', 'house', 'apartment', 'find', 'search', 'عقار', 'شقة', 'بحث'],
            'price_inquiry' => ['price', 'cost', 'budget', 'سعر', 'تكلفة', 'الميزانية'],
            'booking_inquiry' => ['book', 'viewing', 'schedule', 'حجز', 'موعد', 'معاينة'],
            'contact_request' => ['contact', 'agent', 'help', 'تواصل', 'وكيل', 'مساعدة'],
            'location_inquiry' => ['location', 'area', 'where', 'موقع', 'منطقة', 'أين'],
            'features_inquiry' => ['bedroom', 'bathroom', 'features', 'غرفة نوم', 'حمام', 'مميزات'],
            default => [],
        };
    }

    /**
     * Check if message requires agent escalation.
     */
    public function requiresEscalation(string $intent, float $confidence): bool
    {
        // Low confidence responses should be escalated
        if ($confidence < 0.5) {
            return true;
        }

        // Specific intents that should be escalated
        if (in_array($intent, ['contact_request'])) {
            return false; // Already suggesting escalation in response
        }

        return false;
    }
}
