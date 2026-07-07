<?php

namespace App\Filament\Resources\SalesCars\Pages;

use App\Filament\Resources\SalesCars\SalesCarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalesCars extends ListRecords
{
    protected static string $resource = SalesCarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
