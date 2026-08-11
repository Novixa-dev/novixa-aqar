<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Property;
use App\Models\Team;
use App\Models\User;
use App\Services\LeadScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_submits_property_lead_inquiry_form_successfully()
    {
        $team = Team::factory()->create();
        $property = Property::factory()->create([
            'team_id' => $team->id,
            'title' => 'شقة فاخرة للبيع بالمكلا',
        ]);

        Livewire::test(\App\Livewire\PropertyDetail::class, ['propertyId' => $property->id])
            ->set('name', 'علي عمر باوزير')
            ->set('email', 'ali.bawazir@example.com')
            ->set('phone', '+967 770 123 456')
            ->set('message', 'أود معرفة تفاصيل السعر وطريقة السداد.')
            ->call('submitLeadForm')
            ->assertHasNoErrors()
            ->assertDispatched('leadSubmitted');

        $this->assertDatabaseHas('leads', [
            'name' => 'علي عمر باوزير',
            'email' => 'ali.bawazir@example.com',
            'phone' => '+967 770 123 456',
            'status' => 'new',
            'team_id' => $team->id,
        ]);

        $lead = Lead::where('email', 'ali.bawazir@example.com')->first();
        $this->assertNotNull($lead);
        $this->assertGreaterThan(0, $lead->score);
        $this->assertContains($lead->category, ['hot', 'warm', 'cold']);

        $this->assertDatabaseHas('activities', [
            'lead_id' => $lead->id,
            'type' => 'property_inquiry',
        ]);
    }

    public function test_calculates_hot_warm_cold_lead_categories()
    {
        $team = Team::factory()->create();

        $lead = Lead::create([
            'name' => 'سالم العمودي',
            'email' => 'salem.alamoudi@example.com',
            'phone' => '+967771234567',
            'message' => 'مهتم بالشراء الفوري مع الدفع نقداً.',
            'status' => 'new',
            'score' => 85,
            'team_id' => $team->id,
        ]);

        $lead->updateCategory();
        $this->assertEquals('hot', $lead->category);

        $lead->update(['score' => 60]);
        $lead->updateCategory();
        $this->assertEquals('warm', $lead->category);

        $lead->update(['score' => 20]);
        $lead->updateCategory();
        $this->assertEquals('cold', $lead->category);
    }
}
