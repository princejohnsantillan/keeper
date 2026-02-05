<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Keepers\Pages;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Actions\InviteKeeperAction;
use App\Filament\Panels\Keeper\Resources\Keepers\KeeperResource;
use Filament\Resources\Pages\ListRecords;

final class ListKeepers extends ListRecords
{
    protected static string $resource = KeeperResource::class;

    /**
     * @return array<\Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        $currentKeeper = app(GetCurrentKeeperAction::class)->__invoke();

        if (! $currentKeeper->isAdmin()) {
            return [];
        }

        return [
            InviteKeeperAction::make(),
        ];
    }
}
