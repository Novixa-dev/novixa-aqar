<?php

namespace Tests\Feature;

use App\Filament\App\Pages\Tenant\Profile;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The shared "Profile" page registered in eight role panels.
 */
class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('tenant'));
    }

    public function test_a_user_without_reviews_sees_a_dash_not_a_number(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->assertSet('data.average_rating', '—');
    }

    public function test_saving_updates_the_name_and_email(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('data.name', 'Salem Bawazir')
            ->set('data.email', 'salem@example.test')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertNotified();

        $user->refresh();
        $this->assertSame('Salem Bawazir', $user->name);
        $this->assertSame('salem@example.test', $user->email);
    }

    public function test_an_email_taken_by_another_account_is_a_validation_error(): void
    {
        $other = User::factory()->create(['email' => 'taken@example.test']);
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('data.email', 'taken@example.test')
            ->call('submit')
            ->assertHasErrors(['data.email']);

        $this->assertNotSame('taken@example.test', $user->refresh()->email);
        $this->assertSame('taken@example.test', $other->refresh()->email);
    }

    public function test_the_admin_panel_copy_saves_too(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Filament\Admin\Pages\Tenant\Profile::class)
            ->set('data.name', 'Admin Renamed')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertNotified();

        $this->assertSame('Admin Renamed', $user->refresh()->name);
    }

    public function test_the_rating_field_cannot_be_written(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('data.average_rating', '5')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertArrayNotHasKey('average_rating', $user->refresh()->getAttributes());
    }
}
