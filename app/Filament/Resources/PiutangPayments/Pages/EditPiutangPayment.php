<?php

namespace App\Filament\Resources\PiutangPayments\Pages;

use App\Filament\Resources\PiutangPayments\PiutangPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPiutangPayment extends EditRecord
{
    protected static string $resource = PiutangPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
