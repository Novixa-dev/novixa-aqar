<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyModerationVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function listing(string $title, ?string $moderation, array $extra = []): Property
    {
        return Property::factory()->create($extra + ['title' => $title, 'status' => 'for_sale', 'moderation_status' => $moderation]);
    }

    public function test_only_approved_listings_appear_on_the_public_list(): void
    {
        $this->listing('عقار معتمد للعرض', 'approved');
        $this->listing('عقار قيد المراجعة', 'pending');
        $this->listing('عقار مرفوض', 'rejected');

        $this->assertSame(['عقار معتمد للعرض'], Property::publiclyVisible()->pluck('title')->all());
        $this->get('/properties')->assertOk()->assertSee('عقار معتمد للعرض')->assertDontSee('عقار قيد المراجعة')->assertDontSee('عقار مرفوض');
    }

    public function test_an_unapproved_listing_is_hidden_from_visitors_but_not_from_its_owner_or_staff(): void
    {
        $owner = User::factory()->create();
        $pending = $this->listing('عقار قيد المراجعة', 'pending', ['user_id' => $owner->id]);

        $this->get("/properties/{$pending->id}")->assertNotFound();
        $this->actingAs(User::factory()->create())->get("/properties/{$pending->id}")->assertNotFound();
        $this->actingAs($owner)->get("/properties/{$pending->id}")->assertOk();

        // The real seeded staff account (team-scoped role), as in production.
        $this->seed(\Database\Seeders\DefaultTeamSeeder::class);
        $this->seed(\Database\Seeders\RolesSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
        $staff = User::where('email', \App\Support\DemoAccounts::email('staff'))->first();
        $this->actingAs($staff)->get("/properties/{$pending->id}")->assertOk();
    }
}
