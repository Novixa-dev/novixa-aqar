<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\RentalApplication;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RentalApplicationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_route_loads_for_authenticated_verified_user()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create();
        $user->teams()->attach($team);
        $property = Property::factory()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->get(route('rental.apply', ['property_id' => $property->id]));

        $response->assertStatus(200);
    }

    public function test_submitting_the_form_creates_a_rental_application()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create();
        $user->teams()->attach($team);
        $property = Property::factory()->create(['team_id' => $team->id]);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\RentalApplicationForm::class, ['property_id' => $property->id])
            ->set('employment_status', 'employed')
            ->set('annual_income', 50000)
            ->set('lease_start_date', now()->addDays(10)->format('Y-m-d'))
            ->set('lease_end_date', now()->addYear()->format('Y-m-d'))
            ->call('submit');

        $this->assertDatabaseHas('rental_applications', [
            'property_id' => $property->id,
            'tenant_id' => $user->id,
            'employment_status' => 'employed',
        ]);
    }
}
