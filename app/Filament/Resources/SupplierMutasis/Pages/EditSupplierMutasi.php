<?php

namespace App\Filament\Resources\SupplierMutasis\Pages;

use App\Filament\Resources\SupplierMutasis\SupplierMutasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupplierMutasi extends EditRecord
{
    protected static string $resource = SupplierMutasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
