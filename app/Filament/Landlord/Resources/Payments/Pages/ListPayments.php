<?php

namespace App\Filament\Landlord\Resources\Payments\Pages;

use App\Filament\Landlord\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
