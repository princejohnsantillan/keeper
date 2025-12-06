<?php

namespace App\Filament\Keeper\Resources\Keepers\Pages;

use App\Filament\Keeper\Resources\Keepers\KeeperResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKeepers extends ListRecords
{
    protected static string $resource = KeeperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
