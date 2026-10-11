<?php

namespace Tests\Feature;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Notifications\LeaseAgreementReady;
use App\Notifications\MaintenanceRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationEmailsTest extends TestCase
{
    use RefreshDatabase;

    private function render($notification, string $locale): string
    {
        app()->setLocale($locale);
        $user = new User(['name' => 'محمد العمودي', 'email' => 'tenant@novixa.dev']);

        return (string) $notification->toMail($user)->render();
    }

    private function maintenanceRequest(): MaintenanceRequest
    {
        $request = new MaintenanceRequest(['title' => 'تسريب في المطبخ', 'status' => 'pending']);
        $request->id = 42;

        return $request;
    }

    public function test_arabic_email_is_translated_right_to_left_and_branded(): void
    {
        $html = $this->render(new MaintenanceRequestSubmitted($this->maintenanceRequest()), 'ar');

        $this->assertStringContainsString('dir="rtl"', $html);
        $this->assertStringContainsString('تم إرسال طلب الصيانة الخاص بك بنجاح', $html);
        $this->assertStringContainsString(config('branding.name.ar'), $html);
        $this->assertStringNotContainsString('Thank you for using our application', $html);
    }

    public function test_english_email_stays_left_to_right(): void
    {
        $html = $this->render(new MaintenanceRequestSubmitted($this->maintenanceRequest()), 'en');

        $this->assertStringContainsString('dir="ltr"', $html);
        $this->assertStringContainsString('Your maintenance request has been submitted successfully.', $html);
    }

    public function test_email_buttons_link_to_pages_that_exist(): void
    {
        $links = [
            new MaintenanceRequestSubmitted($this->maintenanceRequest()),
            new LeaseAgreementReady(7),
        ];

        foreach ($links as $notification) {
            $url = $notification->toMail(new User(['name' => 'x']))->actionUrl;
            $path = '/'.ltrim(parse_url($url, PHP_URL_PATH), '/');

            $matched = collect(app('router')->getRoutes()->getRoutes())
                ->contains(fn ($route) => in_array('GET', $route->methods())
                    && preg_match('#^'.preg_replace('/\\\\\{[^}]+\\\\\}/', '[^/]+', preg_quote('/'.ltrim($route->uri(), '/'), '#')).'$#', $path));

            $this->assertTrue($matched, class_basename($notification)." links to {$path}, which is not a route");
        }
    }
}
