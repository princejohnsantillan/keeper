<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Guardians\Pages;

use App\Filament\Panels\Keeper\Resources\Guardians\GuardianResource;
use App\Filament\Panels\Keeper\Resources\Guardians\Tables\GuardiansTable;
use Filament\Resources\Pages\ViewRecord;

final class ViewGuardian extends ViewRecord
{
    protected static string $resource = GuardianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            GuardiansTable::getEditAction(),
        ];
    }
}
