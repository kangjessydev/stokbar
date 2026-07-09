<?php

namespace App\Filament\Resources\PiutangPayments\Pages;

use App\Filament\Resources\PiutangPayments\PiutangPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPiutangPayments extends ListRecords
{
    protected static string $resource = PiutangPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
