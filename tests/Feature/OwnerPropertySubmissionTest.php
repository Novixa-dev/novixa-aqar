<?php

namespace Tests\Feature;

use App\Models\Neighborhood;
use App\Models\Property;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OwnerPropertySubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submits_owner_property_submission_form_successfully()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $neighborhood = Neighborhood::factory()->create(['name' => 'حي السلام - المكلا']);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\PropertySubmissionForm::class)
            ->set('title', 'فيلا حديثة للبيع في حي السلام')
            ->set('property_type', 'villa')
            ->set('price', 45000000)
            ->set('location', 'المكلا - حي السلام')
            ->set('neighborhood_id', $neighborhood->id)
            ->set('bedrooms', 5)
            ->set('bathrooms', 4)
            ->set('area_sqft', 350)
            ->set('year_built', 2024)
            ->set('owner_phone', '+967 770 999 888')
            ->set('description', 'فيلا راقية مكونة من دورين مع حوش واسع وكراج سيارة بالقرب من الخدمات.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('propertySubmitted');

        $this->assertDatabaseHas('properties', [
            'title' => 'فيلا حديثة للبيع في حي السلام',
            'property_type' => 'villa',
            'price' => 45000000,
            'neighborhood_id' => $neighborhood->id,
            'internal_notes' => 'رقم هاتف المالك: +967 770 999 888',
            // `status` is the listing's for_sale/for_rent state (defaults to for_sale);
            // `moderation_status` is the separate staff-review gate -- see Property::
            // scopePubliclyVisible(), which treats a NULL moderation_status as already
            // approved, so a public submission must set it explicitly to stay hidden
            // until a staff member reviews it.
            'status' => 'for_sale',
            'moderation_status' => 'pending',
            'user_id' => $user->id,
        ]);

        $property = Property::where('title', 'فيلا حديثة للبيع في حي السلام')->first();
        $this->assertFalse($property->isPubliclyVisible(), 'An unmoderated public submission must not be publicly visible yet.');
    }

    public function test_submitting_as_for_rent_sets_the_canonical_rent_status(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(\App\Livewire\PropertySubmissionForm::class)
            ->set('title', 'شقة للإيجار في حي الشرج')
            ->set('listing_type', 'for_rent')
            ->set('property_type', 'apartment')
            ->set('price', 150000)
            ->set('location', 'المكلا - الشرج')
            ->set('bedrooms', 2)
            ->set('bathrooms', 1)
            ->set('area_sqft', 120)
            ->set('description', 'شقة مفروشة جزئياً قريبة من الخدمات.')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('properties', [
            'title' => 'شقة للإيجار في حي الشرج',
            'status' => 'for_rent',
            'moderation_status' => 'pending',
        ]);
    }
}
