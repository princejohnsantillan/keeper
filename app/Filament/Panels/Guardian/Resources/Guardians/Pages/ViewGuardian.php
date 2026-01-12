<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Pages;

use App\Filament\Panels\Guardian\Resources\Guardians\GuardianResource;
use App\Filament\Panels\Guardian\Resources\Guardians\Tables\GuardiansTable;
use App\Models\Guardian;
use Filament\Resources\Pages\ViewRecord;

final class ViewGuardian extends ViewRecord
{
    protected static string $resource = GuardianResource::class;

    public function getHeading(): string
    {
        /** @var Guardian $record */
        $record = $this->getRecord();

        return $record->full_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            GuardiansTable::getEditAction(),
            GuardiansTable::getDeleteAction(),
        ];
    }
}
