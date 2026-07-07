<?php

namespace App\Filament\Resources\CanvasReturs\Pages;

use App\Filament\Resources\CanvasReturs\CanvasReturResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCanvasRetur extends EditRecord
{
    protected static string $resource = CanvasReturResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
