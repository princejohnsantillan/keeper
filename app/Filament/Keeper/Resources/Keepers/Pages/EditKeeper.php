<?php

namespace App\Filament\Keeper\Resources\Keepers\Pages;

use App\Filament\Keeper\Resources\Keepers\KeeperResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKeeper extends EditRecord
{
    protected static string $resource = KeeperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
