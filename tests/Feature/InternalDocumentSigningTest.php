<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use App\Services\DigitalSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalDocumentSigningTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_document_records_internal_signature_without_external_provider()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['is_signable' => true]);

        $signature = app(DigitalSignatureService::class)
            ->signDocument($user, $document, 'data:image/png;base64,abc123');

        $this->assertDatabaseHas('digital_signatures', [
            'id' => $signature->id,
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
        $this->assertEquals($user->id, $signature->user_id);
        $this->assertNotNull($signature->signed_at);
        $this->assertNotNull($signature->ip_address);
    }

    public function test_sign_document_rejects_non_signable_document()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['is_signable' => false]);

        $this->expectException(\Exception::class);

        app(DigitalSignatureService::class)->signDocument($user, $document, 'sig');
    }

    public function test_verify_signature_passes_for_complete_record()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['is_signable' => true]);

        $signature = app(DigitalSignatureService::class)
            ->signDocument($user, $document, 'data:image/png;base64,abc123');

        $this->assertTrue(app(DigitalSignatureService::class)->verifySignature($signature));
    }
}
