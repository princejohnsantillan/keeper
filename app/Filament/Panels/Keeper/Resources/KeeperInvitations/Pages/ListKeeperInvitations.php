<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\KeeperInvitations\Pages;

use App\Filament\Panels\Keeper\Resources\KeeperInvitations\KeeperInvitationResource;
use Filament\Resources\Pages\ListRecords;

final class ListKeeperInvitations extends ListRecords
{
    protected static string $resource = KeeperInvitationResource::class;

    /**
     * @return array<\Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
