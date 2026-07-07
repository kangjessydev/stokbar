<?php

namespace App\Filament\Resources\ResepBoms\Pages;

use App\Filament\Resources\ResepBoms\ResepBomResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResepBom extends EditRecord
{
    protected static string $resource = ResepBomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
