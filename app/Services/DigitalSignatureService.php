<?php

namespace App\Services;

use Exception;
use App\Models\DigitalSignature;
use App\Models\User;
use App\Models\Document;

/**
 * Internal e-signature flow (no external provider required).
 *
 * Records who signed what, when, from where, and pins the exact document
 * version that was signed. DocuSign remains available as an optional
 * integration via DocuSignService but is not part of the signing path.
 */
class DigitalSignatureService
{
    public function signDocument(User $user, Document $document, $signatureData)
    {
        if (!$document->isSignable()) {
            throw new Exception('This document is not signable.');
        }

        return DigitalSignature::create([
            'user_id' => $user->id,
            'document_id' => $document->id,
            'document_version_id' => $document->versions()->latest('version')->value('id'),
            'signature_data' => $signatureData,
            'signed_at' => now(),
            'team_id' => $user->team_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * A signature is valid when it is complete and internally consistent:
     * signed by a known user on a known document with recorded evidence.
     */
    public function verifySignature(DigitalSignature $signature): bool
    {
        return $signature->signed_at !== null
            && filled($signature->signature_data)
            && $signature->user_id !== null
            && $signature->document_id !== null
            && $signature->user()->exists()
            && $signature->document()->exists();
    }
}
