<?php

namespace App\Filament\Resources\CanvasMuatans\Pages;

use App\Filament\Resources\CanvasMuatans\CanvasMuatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCanvasMuatan extends EditRecord
{
    protected static string $resource = CanvasMuatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
