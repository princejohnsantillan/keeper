<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Guardians\Pages;

use App\Filament\Panels\Keeper\Resources\Guardians\GuardianResource;
use Filament\Resources\Pages\ListRecords;

final class ListGuardians extends ListRecords
{
    protected static string $resource = GuardianResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
