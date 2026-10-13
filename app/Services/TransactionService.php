<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\Transaction;
use App\Models\Document;
use App\Services\DigitalSignatureService;

class TransactionService
{
    protected $digitalSignatureService;

    public function __construct(DigitalSignatureService $digitalSignatureService)
    {
        $this->digitalSignatureService = $digitalSignatureService;
    }

    public function createTransaction(array $data)
    {
        $transaction = Transaction::create($data);
        $transaction->calculateCommission();
        return $transaction;
    }

    public function updateTransactionStatus(Transaction $transaction, string $status)
    {
        $transaction->update(['status' => $status]);
        return $transaction;
    }

    public static function saleAgreementTemplate(): ?DocumentTemplate
    {
        return DocumentTemplate::where('type', 'sale_agreement')->first();
    }

    /**
     * Render the sale agreement for a transaction and file it as a document
     * linked to that transaction. Null when no sale-agreement template exists.
     */
    public function generateContractualDocument(Transaction $transaction): ?Document
    {
        $documentTemplate = static::saleAgreementTemplate();

        if (! $documentTemplate) {
            return null;
        }

        $content = $documentTemplate->renderContent([
            'buyer' => $transaction->user?->name,
            'seller' => $transaction->seller?->name,
            'property' => $transaction->property?->title,
            'amount' => $transaction->amount,
            'date' => $transaction->date?->format('Y-m-d'),
        ]);

        // content is not mass-assignable on Document (uploads set file_path).
        $document = (new Document)->forceFill([
            'title' => 'Sale Agreement - '.($transaction->property?->title ?? '#'.$transaction->id),
            'content' => $content,
            'team_id' => $transaction->property?->team_id,
        ]);
        $document->documentable()->associate($transaction);
        $document->save();

        return $document;
    }

    public function signDocument(Document $document, $user, $signatureData)
    {
        return $this->digitalSignatureService->signDocument($user, $document, $signatureData);
    }
}