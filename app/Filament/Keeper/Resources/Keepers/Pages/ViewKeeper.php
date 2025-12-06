<?php

namespace App\Filament\Keeper\Resources\Keepers\Pages;

use App\Filament\Keeper\Resources\Keepers\KeeperResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKeeper extends ViewRecord
{
    protected static string $resource = KeeperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
