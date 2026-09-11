<?php

namespace App\Services;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * The staff-facing assistant: answers free-text questions and drafts tenant
 * messages.
 *
 * Like AIDescriptionService this works with or without a text-generation
 * provider. Without one it answers through ChatbotService, which is
 * rule-based and already understands both Arabic and English, so the
 * assistant stays genuinely useful rather than showing an error.
 *
 * It also no longer throws. The Livewire component caught the exception and
 * printed its raw English message into the response box -- in an
 * Arabic-first product, on a screen where the user asked a question and got
 * "Error: Failed to generate AI response" back.
 */
class AIAssistantService
{
    public function __construct(private readonly ChatbotService $chatbot) {}

    public function generateResponse($input, $context): string
    {
        $remote = $this->generateRemotely((string) $input, (string) $context);

        if ($remote !== null) {
            return $remote;
        }

        return $this->chatbot->processMessage((string) $input)['message'];
    }

    public function usesRemoteProvider(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    /**
     * @return string|null the generated text, or null to fall back locally
     */
    private function generateRemotely(string $input, string $context): ?string
    {
        if (! $this->usesRemoteProvider()) {
            return null;
        }

        try {
            // The previous endpoint here was /v1/engines/davinci-codex/
            // completions, which OpenAI removed years ago -- this call could
            // never have succeeded, key or no key.
            $response = Http::withToken(config('services.openai.api_key'))
                ->timeout((int) config('services.openai.timeout', 20))
                ->asJson()
                ->post(rtrim((string) config('services.openai.base_uri'), '/').'/chat/completions', [
                    'model' => config('services.openai.chat_model'),
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => "Context: {$context}\n\nQuestion: {$input}"],
                    ],
                    'max_tokens' => 400,
                    'temperature' => 0.7,
                ]);

            if (! $response->successful()) {
                Log::warning('AI assistant provider returned an error; answering locally instead.', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $text = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

            return $text !== '' ? $text : null;
        } catch (Throwable $e) {
            Log::warning('AI assistant provider was unreachable; answering locally instead.', [
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function systemPrompt(): string
    {
        return app()->getLocale() === 'ar'
            ? 'أنت مساعد ذكي لإدارة العقارات في منصة عقارية يمنية. أجب بالعربية بإيجاز ووضوح.'
            : 'You are an assistant for a real-estate management platform. Answer concisely and clearly.';
    }

    public function scheduleMaintenance(MaintenanceRequest $request): string
    {
        $request->update(['status' => 'scheduled']);

        $date = now()->addDays(3)->toDateString();
        $address = $request->property->address ?? '';

        return app()->getLocale() === 'ar'
            ? "تمت جدولة الصيانة للعقار {$address} بتاريخ {$date}."
            : "Maintenance scheduled for {$address} on {$date}.";
    }

    public function generateFinancialReport(Property $property): string
    {
        $income = (float) $property->leases()->sum('rent_amount');
        $expenses = (float) $property->maintenanceRequests()->sum('cost');
        $profit = $income - $expenses;

        // These were written as "${$income}" -- PHP reads that as a variable
        // variable, not a dollar sign followed by a value, so every figure in
        // this report rendered blank.
        $format = fn (float $amount): string => app()->getLocale() === 'ar'
            ? number_format($amount, 2).' ريال يمني'
            : number_format($amount, 2).' YER';

        $address = $property->address ?? '';

        if (app()->getLocale() === 'ar') {
            return "التقرير المالي للعقار {$address}:\n"
                ."الإيرادات: {$format($income)}\n"
                ."المصروفات: {$format($expenses)}\n"
                ."صافي الربح: {$format($profit)}";
        }

        return "Financial Report for {$address}:\n"
            ."Income: {$format($income)}\n"
            ."Expenses: {$format($expenses)}\n"
            ."Profit: {$format($profit)}";
    }

    public function generateTenantCommunication(User $tenant, $subject): string
    {
        $property = $tenant->property->address ?? '';
        $context = "Tenant: {$tenant->name}, Property: {$property}, Subject: {$subject}";

        $input = app()->getLocale() === 'ar'
            ? "اكتب رسالة مهذبة ومهنية للمستأجر بخصوص: {$subject}"
            : "Write a polite, professional message to the tenant regarding: {$subject}";

        $remote = $this->generateRemotely($input, $context);

        if ($remote !== null) {
            return $remote;
        }

        return app()->getLocale() === 'ar'
            ? "عزيزي/عزيزتي {$tenant->name}،\n\n"
                ."نتواصل معك بخصوص {$subject} فيما يتعلق بالعقار {$property}.\n\n"
                ."يسعدنا الرد على أي استفسار لديك عبر المنصة.\n\nمع التحية،\nفريق إدارة العقارات"
            : "Dear {$tenant->name},\n\n"
                ."We are writing to you regarding {$subject} in connection with {$property}.\n\n"
                ."We are happy to answer any questions through the platform.\n\nKind regards,\nProperty Management";
    }
}
