<?php

namespace App\Filament\Guardian\Resources\Children\Pages;

use App\Filament\Guardian\Resources\Children\ChildResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChildren extends ListRecords
{
    protected static string $resource = ChildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
