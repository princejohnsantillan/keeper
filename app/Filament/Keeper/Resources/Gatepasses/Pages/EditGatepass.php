<?php

namespace App\Filament\Keeper\Resources\Gatepasses\Pages;

use App\Filament\Keeper\Resources\Gatepasses\GatepassResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGatepass extends EditRecord
{
    protected static string $resource = GatepassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
