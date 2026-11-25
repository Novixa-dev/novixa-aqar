<?php

namespace Tests\Feature;

use App\Livewire\PropertyDetail;
use App\Livewire\PropertyReviewForm;
use App\Models\ContactMessage;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Public forms that store a record and notify the office must be capped
 * per IP and bounded in size, like PropertyRequestForm already was.
 */
class PublicFormAbuseLimitsTest extends TestCase
{
    use RefreshDatabase;

    private function contactPayload(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'سالم',
            'email' => 'salem@example.test',
            'message' => 'أرغب في معاينة شقة في المكلا.',
        ];
    }

    public function test_the_contact_form_is_capped_per_ip_with_a_localized_error(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->from('/contact')->post('/contact', $this->contactPayload())->assertSessionHasNoErrors();
        }

        $this->from('/contact')->post('/contact', $this->contactPayload())
            ->assertRedirect('/contact')
            ->assertSessionHasErrors('message');

        $this->assertSame(5, ContactMessage::count());
    }

    public function test_the_contact_message_length_is_bounded(): void
    {
        Mail::fake();

        $this->from('/contact')->post('/contact', $this->contactPayload(['message' => str_repeat('a', 5001)]))
            ->assertSessionHasErrors('message');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_the_property_inquiry_form_is_capped_per_ip(): void
    {
        $team = Team::factory()->create();
        $property = Property::factory()->create(['team_id' => $team->id]);

        $component = Livewire::test(PropertyDetail::class, ['propertyId' => $property->id]);
        // The form clears itself after each successful inquiry.
        $fill = fn () => $component->set('name', 'علي')->set('email', 'ali@example.test')->set('message', 'ما هو السعر النهائي؟');

        for ($i = 0; $i < 5; $i++) {
            $fill()->call('submitLeadForm')->assertHasNoErrors();
        }
        $fill()->call('submitLeadForm')->assertHasErrors('name');

        $this->assertSame(5, Lead::count());
    }

    public function test_the_property_inquiry_message_length_is_bounded(): void
    {
        $property = Property::factory()->create();

        Livewire::test(PropertyDetail::class, ['propertyId' => $property->id])
            ->set('name', 'علي')
            ->set('email', 'ali@example.test')
            ->set('message', str_repeat('a', 5001))
            ->call('submitLeadForm')
            ->assertHasErrors(['message' => 'max']);

        $this->assertSame(0, Lead::count());
    }

    public function test_a_review_submitted_after_the_session_expired_errors_instead_of_crashing(): void
    {
        $property = Property::factory()->create();

        Livewire::test(PropertyReviewForm::class, ['propertyId' => $property->id])
            ->set('rating', 5)
            ->set('title', 'تجربة ممتازة')
            ->set('comment', 'تجربة ممتازة مع المكتب والعقار.')
            ->call('submitReview')
            ->assertHasErrors('general');
    }
}
