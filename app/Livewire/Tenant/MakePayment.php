<?php

namespace App\Livewire\Tenant;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class MakePayment extends Component
{
    public $amount;
    public $paymentMethod;
    public $invoiceId;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'paymentMethod' => 'required|in:credit_card,bank_transfer',
        'invoiceId' => 'required|exists:invoices,id',
    ];

    public function mount()
    {
        // Invoice statuses are pending/paid/void — 'unpaid' never matched.
        $this->invoiceId = Invoice::where('tenant_id', auth()->id())
            ->whereIn('status', ['pending'])
            ->orderBy('id')
            ->first()->id ?? null;
    }

    public function createPaymentIntent()
    {
        $this->validate();

        // Ownership guard: never create intents for another tenant's invoice.
        Invoice::query()
            ->whereKey($this->invoiceId)
            ->where('tenant_id', auth()->id())
            ->whereNotIn('status', ['paid', 'void'])
            ->firstOrFail();

        Stripe::setApiKey((string) config('stripe.secret_key'));

        $paymentIntent = PaymentIntent::create([
            'amount' => (int) round(((float) $this->amount) * 100),
            'currency' => 'usd',
            'metadata' => [
                'invoice_id' => (string) $this->invoiceId,
                'tenant_id' => (string) auth()->id(),
            ],
        ]);

        return $paymentIntent->client_secret;
    }

    public function processPayment()
    {
        $this->validate();

        // Ownership + status guard: a tampered invoiceId must not be able to
        // record payments against someone else's (or an already-paid) invoice.
        $invoice = Invoice::query()
            ->whereKey($this->invoiceId)
            ->where('tenant_id', auth()->id())
            ->whereNotIn('status', ['paid', 'void'])
            ->first();

        if (! $invoice) {
            $this->addError('invoiceId', __('The selected invoice is invalid.'));

            return;
        }

        Payment::create([
            'amount' => $this->amount,
            'payment_date' => now(),
            'status' => 'completed',
            'payment_method' => $this->paymentMethod,
            'tenant_id' => auth()->id(),
            'invoice_id' => $invoice->id,
        ]);

        $invoice->update(['status' => 'paid']);

        $this->dispatch('paymentProcessed');
        session()->flash('message', __('Payment processed successfully.'));
    }

    public function render()
    {
        return view('livewire.tenant.make-payment');
    }
}