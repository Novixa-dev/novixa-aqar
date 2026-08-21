<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function webhookPayload(array $intentOverrides = []): array
    {
        return [
            'id' => 'evt_test_'.Str::random(8),
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => array_merge([
                    'id' => 'pi_'.Str::random(10),
                    'object' => 'payment_intent',
                    'status' => 'succeeded',
                    'amount_received' => 15000,
                    'currency' => 'usd',
                    'metadata' => [
                        'invoice_id' => '1',
                        'tenant_id' => '1',
                    ],
                ], $intentOverrides),
            ],
        ];
    }

    private function signedWebhookHeaders(array $payload, string $secret): array
    {
        $timestamp = time();
        $signed = $timestamp.'.'.json_encode($payload);
        $signature = hash_hmac('sha256', $signed, $secret);

        return ['Stripe-Signature' => "t={$timestamp},v1={$signature}"];
    }

    public function test_create_session_rejects_missing_invoice_id(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/payments/session', [])
            ->assertStatus(422);
    }

    public function test_create_session_cannot_target_another_tenants_invoice(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        Invoice::factory()->create(['tenant_id' => $owner->id, 'status' => 'pending']);

        $this->actingAs($attacker)
            ->postJson('/payments/session', ['invoice_id' => 1])
            ->assertStatus(404);

        $this->assertDatabaseMissing('payments', ['tenant_id' => $attacker->id]);
    }

    public function test_create_session_rejects_already_paid_invoice_before_stripe_call(): void
    {
        $owner = User::factory()->create();
        $invoice = Invoice::factory()->create(['tenant_id' => $owner->id, 'status' => 'paid']);

        // No Stripe key configured: if ownership checks failed we would get a
        // Stripe auth error, not the clean 404 this asserts.
        config(['stripe.secret_key' => '']);

        $this->actingAs($owner)
            ->postJson('/payments/session', ['invoice_id' => $invoice->id])
            ->assertStatus(404);
    }

    public function test_webhook_rejects_invalid_signature_without_recording_payment(): void
    {
        config(['stripe.webhook_secret' => 'whsec_test_secret']);
        $payload = $this->webhookPayload();

        $this->postJson('/payments/webhook', $payload, ['Stripe-Signature' => 't=1,v1=invalid'])
            ->assertStatus(400);

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_webhook_records_payment_and_marks_invoice_paid(): void
    {
        config(['stripe.webhook_secret' => 'whsec_test_secret']);
        $tenant = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'tenant_id' => $tenant->id,
            'amount' => 150,
            'status' => 'pending',
        ]);
        $payload = $this->webhookPayload([
            'metadata' => ['invoice_id' => (string) $invoice->id, 'tenant_id' => (string) $tenant->id],
        ]);

        $this->postJson('/payments/webhook', $payload, $this->signedWebhookHeaders($payload, 'whsec_test_secret'))
            ->assertOk()
            ->assertJsonPath('received', true);

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id,
            'status' => 'completed',
            'payment_method' => 'stripe',
            'amount' => 150,
        ]);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
    }

    public function test_webhook_is_idempotent_when_the_same_event_replays(): void
    {
        config(['stripe.webhook_secret' => 'whsec_test_secret']);
        $tenant = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'tenant_id' => $tenant->id,
            'amount' => 150,
            'status' => 'pending',
        ]);
        $payload = $this->webhookPayload([
            'id' => 'pi_replay_test',
            'metadata' => ['invoice_id' => (string) $invoice->id, 'tenant_id' => (string) $tenant->id],
        ]);
        $headers = $this->signedWebhookHeaders($payload, 'whsec_test_secret');

        $this->postJson('/payments/webhook', $payload, $headers)->assertOk();
        $this->postJson('/payments/webhook', $payload, $headers)->assertOk();

        $this->assertSame(1, Payment::where('payment_intent_id', 'pi_replay_test')->count());
        $this->assertSame(1, Payment::count());
    }

    public function test_webhook_ignores_intents_whose_amount_does_not_match_invoice(): void
    {
        config(['stripe.webhook_secret' => 'whsec_test_secret']);
        $tenant = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'tenant_id' => $tenant->id,
            'amount' => 150,
            'status' => 'pending',
        ]);
        $payload = $this->webhookPayload([
            'amount_received' => 100, // invoice expects 15000 cents
            'metadata' => ['invoice_id' => (string) $invoice->id, 'tenant_id' => (string) $tenant->id],
        ]);

        $this->postJson('/payments/webhook', $payload, $this->signedWebhookHeaders($payload, 'whsec_test_secret'))
            ->assertOk();

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'pending']);
    }

    public function test_webhook_ignores_non_succeeded_intent_status(): void
    {
        config(['stripe.webhook_secret' => 'whsec_test_secret']);
        $tenant = User::factory()->create();
        $invoice = Invoice::factory()->create(['tenant_id' => $tenant->id, 'status' => 'pending']);
        $payload = $this->webhookPayload([
            'status' => 'requires_capture',
            'metadata' => ['invoice_id' => (string) $invoice->id, 'tenant_id' => (string) $tenant->id],
        ]);

        $this->postJson('/payments/webhook', $payload, $this->signedWebhookHeaders($payload, 'whsec_test_secret'))
            ->assertOk();

        $this->assertDatabaseCount('payments', 0);
    }
}
