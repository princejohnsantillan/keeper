<?php

namespace App\Filament\Panels\Keeper\Resources\Gatepasses\Pages;

use App\Filament\Panels\Keeper\Resources\Gatepasses\GatepassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGatepasses extends ListRecords
{
    protected static string $resource = GatepassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->label('Add gatepass')
                ->modalHeading('Add gatepass')
                ->modalSubmitActionLabel('Add')
                ->createAnother(false),
        ];
    }
}
