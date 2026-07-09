<?php

namespace App\Filament\Resources\SupplierMutasis\Pages;

use App\Filament\Resources\SupplierMutasis\SupplierMutasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupplierMutasis extends ListRecords
{
    protected static string $resource = SupplierMutasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
