<?php

namespace App\Filament\Guardian\Resources\Activities\Pages;

use App\Filament\Guardian\Resources\Activities\ActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
