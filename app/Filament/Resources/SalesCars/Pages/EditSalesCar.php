<?php

namespace App\Filament\Resources\SalesCars\Pages;

use App\Filament\Resources\SalesCars\SalesCarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalesCar extends EditRecord
{
    protected static string $resource = SalesCarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
