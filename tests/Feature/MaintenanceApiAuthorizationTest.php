<?php

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MaintenanceApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Team $team;

    private Property $leasedProperty;

    private Property $otherProperty;

    private User $tenantUser;

    private Tenant $tenant;

    private Tenant $otherTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $owner->id]);
        $owner->forceFill(['current_team_id' => $this->team->id])->save();

        $this->leasedProperty = Property::factory()->for($this->team)->create();
        $this->otherProperty = Property::factory()->for($this->team)->create();

        $this->tenantUser = $this->member();
        $this->tenant = Tenant::create(['team_id' => $this->team->id, 'user_id' => $this->tenantUser->id, 'name' => 'Own Tenant', 'email' => 'own.tenant@example.test']);
        $this->otherTenant = Tenant::create(['team_id' => $this->team->id, 'name' => 'Other Tenant', 'email' => 'other.tenant@example.test']);

        Lease::create([
            'property_id' => $this->leasedProperty->id,
            'tenant_id' => $this->tenantUser->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addYear(),
            'rent_amount' => 1000,
            'status' => 'active',
        ]);
    }

    private function member(?string $email = null): User
    {
        $user = User::factory()->create($email ? ['email' => $email] : []);
        $this->team->users()->attach($user, ['role' => 'member']);
        $user->forceFill(['current_team_id' => $this->team->id])->save();

        return $user;
    }

    private function request(array $overrides = []): MaintenanceRequest
    {
        return MaintenanceRequest::create($overrides + [
            'team_id' => $this->team->id,
            'tenant_id' => $this->tenant->id,
            'property_id' => $this->leasedProperty->id,
            'title' => 'Leaking tap',
            'description' => 'Kitchen tap drips.',
            'requested_date' => now()->toDateString(),
        ]);
    }

    public function test_a_tenant_cannot_file_a_request_under_another_tenants_name(): void
    {
        Sanctum::actingAs($this->tenantUser);

        $response = $this->postJson('/api/v1/maintenance', [
            'tenant_id' => $this->otherTenant->id,
            'property_id' => $this->leasedProperty->id,
            'title' => 'Broken heater',
            'description' => 'No heat.',
            'requested_date' => now()->toDateString(),
            'status' => 'completed',
            'payment_status' => 'paid',
        ])->assertCreated();

        $response->assertJsonPath('data.tenant_id', $this->tenant->id);
        $this->assertNotSame('completed', $response->json('data.status'));
        $this->assertNotSame('paid', $response->json('data.payment_status'));
    }

    public function test_a_tenant_cannot_file_a_request_for_a_property_they_do_not_lease(): void
    {
        Sanctum::actingAs($this->tenantUser);

        $this->postJson('/api/v1/maintenance', [
            'property_id' => $this->otherProperty->id,
            'title' => 'Broken heater',
            'description' => 'No heat.',
            'requested_date' => now()->toDateString(),
        ])->assertUnprocessable()->assertJsonValidationErrors('property_id');
    }

    public function test_a_tenant_cannot_reassign_or_settle_their_own_request(): void
    {
        $request = $this->request();
        Sanctum::actingAs($this->tenantUser);

        $this->patchJson("/api/v1/maintenance/{$request->id}", [
            'tenant_id' => $this->otherTenant->id,
            'payment_status' => 'paid',
            'description' => 'Now dripping faster.',
        ])->assertOk();

        $request->refresh();
        $this->assertSame($this->tenant->id, $request->tenant_id);
        $this->assertNotSame('paid', $request->payment_status);
        $this->assertSame('Now dripping faster.', $request->description);
    }

    public function test_a_member_with_no_tenant_or_vendor_link_cannot_create_requests(): void
    {
        Sanctum::actingAs($this->member());

        $this->postJson('/api/v1/maintenance', [
            'tenant_id' => $this->tenant->id,
            'property_id' => $this->leasedProperty->id,
            'title' => 'Spam',
            'description' => 'Spam.',
            'requested_date' => now()->toDateString(),
        ])->assertForbidden();
    }

    public function test_a_vendor_can_progress_their_job_but_not_reassign_or_delete_it(): void
    {
        $vendorUser = $this->member('jobs@vendor.example.test');
        $vendor = Vendor::create(['team_id' => $this->team->id, 'company_name' => 'Fix-It Ltd', 'email' => $vendorUser->email, 'vendor_type' => 'plumber']);
        $request = $this->request(['vendor_id' => $vendor->id]);
        Sanctum::actingAs($vendorUser);

        $this->patchJson("/api/v1/maintenance/{$request->id}", [
            'status' => 'in_progress',
            'tenant_id' => $this->otherTenant->id,
        ])->assertOk();

        $request->refresh();
        $this->assertSame('in_progress', $request->status);
        $this->assertSame($this->tenant->id, $request->tenant_id);

        $this->deleteJson("/api/v1/maintenance/{$request->id}")->assertForbidden();
        $this->assertModelExists($request);
    }

    public function test_the_team_owner_keeps_full_access(): void
    {
        $request = $this->request();
        Sanctum::actingAs($this->team->owner);

        $this->patchJson("/api/v1/maintenance/{$request->id}", ['tenant_id' => $this->otherTenant->id])->assertOk();
        $this->assertSame($this->otherTenant->id, $request->refresh()->tenant_id);

        $this->deleteJson("/api/v1/maintenance/{$request->id}")->assertNoContent();
    }
}
