<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Models\User;
use App\Support\PanelSwitcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelSwitcherTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPanelRole(string $role): User
    {
        config()->set('filament-shield.panels', [
            'admin' => ['admin', 'super_admin'],
            'app' => ['admin', 'super_admin', 'staff'],
            'staff' => ['admin', 'super_admin', 'staff'],
            'agent' => ['admin', 'super_admin', 'agent'],
            'buyer' => ['admin', 'super_admin', 'buyer'],
            'contractor' => ['admin', 'super_admin', 'contractor'],
            'landlord' => ['admin', 'super_admin', 'landlord'],
            'seller' => ['admin', 'super_admin', 'seller'],
            'tenant' => ['admin', 'super_admin', 'tenant'],
        ]);

        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }

    public function test_excludes_the_current_panel_from_its_own_menu(): void
    {
        $user = $this->userWithPanelRole('admin');
        $this->actingAs($user);

        $items = PanelSwitcher::menuItems('admin');
        $visible = array_filter($items, fn ($item) => $item->isVisible());

        foreach ($items as $item) {
            // No item should link back to /admin
            $this->assertStringNotContainsString('/admin', (string) $item->getUrl());
        }
        $this->assertCount(8, $visible);
    }

    public function test_buyer_sees_no_switch_links(): void
    {
        $user = $this->userWithPanelRole('buyer');
        $this->actingAs($user);

        $visible = array_filter(PanelSwitcher::menuItems('buyer'), fn ($item) => $item->isVisible());

        $this->assertSame([], $visible);
    }

    public function test_staff_only_sees_the_app_panel(): void
    {
        $user = $this->userWithPanelRole('staff');
        $this->actingAs($user);

        $visible = array_values(array_filter(
            PanelSwitcher::menuItems('staff'),
            fn ($item) => $item->isVisible()
        ));

        $this->assertCount(1, $visible);
        $this->assertStringContainsString('/app', (string) $visible[0]->getUrl());
    }

    public function test_guests_see_nothing(): void
    {
        auth()->logout();

        $visible = array_filter(PanelSwitcher::menuItems('admin'), fn ($item) => $item->isVisible());

        $this->assertSame([], $visible);
    }
}
