<?php

namespace Tests\Feature;

use App\Filament\Landlord\Resources\Payments\Pages\ViewPayment;
use App\Filament\Staff\Resources\AgentPerformances\Pages\ViewAgentPerformance;
use App\Filament\Staff\Resources\ChatConversations\ChatConversationResource\Pages\ViewChatConversation;
use App\Filament\Staff\Resources\Transactions\Pages\ListTransactions;
use App\Models\ChatConversation;
use App\Models\Document;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Panel pages that crashed on first render (wrong Filament namespaces,
 * undefined variables, missing imports) — none had a test before.
 */
class PanelPageRegressionsTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        $this->seed(PermissionsSeeder::class);
        $this->seed(RolesSeeder::class);

        $team = Team::factory()->create();
        $staff = User::factory()->create();
        $staff->teams()->attach($team);
        $staff->forceFill(['current_team_id' => $team->id])->save();
        $staff->assignRole(Role::where('name', 'staff')->where('guard_name', 'web')->first());

        return $staff;
    }

    public function test_staff_agent_performance_view_renders(): void
    {
        $staff = $this->staff();
        $agent = User::factory()->create();
        $agent->assignRole('agent');
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        Livewire::actingAs($staff)
            ->test(ViewAgentPerformance::class, ['record' => $agent->getKey()])
            ->assertOk();
    }

    public function test_staff_chat_conversation_view_renders(): void
    {
        $staff = $this->staff();
        $conversation = ChatConversation::create([
            'session_id' => 'sess-1',
            'status' => 'archived', // not one of the colour-mapped states
        ]);
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        Livewire::actingAs($staff)
            ->test(ViewChatConversation::class, ['record' => $conversation->getKey()])
            ->assertOk();
    }

    public function test_landlord_payment_view_renders(): void
    {
        $this->seed(PermissionsSeeder::class);
        $this->seed(RolesSeeder::class);
        $landlord = User::factory()->create();
        $landlord->assignRole('landlord');
        $tenant = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $landlord->id]);
        Lease::factory()->create(['property_id' => $property->id, 'tenant_id' => $tenant->id]);
        $payment = Payment::factory()->create(['tenant_id' => $tenant->id]);
        Filament::setCurrentPanel(Filament::getPanel('landlord'));

        Livewire::actingAs($landlord)
            ->test(ViewPayment::class, ['record' => $payment->getKey()])
            ->assertOk()
            ->assertSet('data.tenant_name', $tenant->name);
    }

    public function test_generate_document_is_hidden_without_a_sale_agreement_template(): void
    {
        $staff = $this->staff();
        $transaction = Transaction::factory()->create();
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        Livewire::actingAs($staff)
            ->test(ListTransactions::class)
            ->assertTableActionHidden('generateDocument', $transaction);

        $this->assertNull(app(TransactionService::class)->generateContractualDocument($transaction));
        $this->assertSame(0, Document::count());
    }
}
