<?php

namespace Tests\Feature;

use App\Models\Buyer;
use App\Models\Neighborhood;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_submits_property_request_form_successfully()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $neighborhood = Neighborhood::factory()->create(['name' => 'حي فوه - المكلا']);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\PropertyRequestForm::class)
            ->set('name', 'سالم باوزير')
            ->set('phone', '+967 771 222 333')
            ->set('email', 'salem@example.com')
            ->set('property_type', 'apartment')
            ->set('neighborhood_id', $neighborhood->id)
            ->set('min_price', 15000000)
            ->set('max_price', 30000000)
            ->set('bedrooms', 3)
            ->set('bathrooms', 2)
            ->set('notes', 'مطلوب شقة دور ثان بالقرب من الخدمات')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('propertyRequestSubmitted');

        $this->assertDatabaseHas('buyers', [
            'name' => 'سالم باوزير',
            'phone' => '+967 771 222 333',
            'email' => 'salem@example.com',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $buyer = Buyer::where('phone', '+967 771 222 333')->first();
        $this->assertNotNull($buyer);
        $this->assertEquals('apartment', $buyer->search_criteria['property_type']);
        $this->assertEquals($neighborhood->id, $buyer->search_criteria['neighborhood_id']);
        $this->assertEquals(15000000, $buyer->search_criteria['min_price']);
        $this->assertEquals(30000000, $buyer->search_criteria['max_price']);
    }
}
