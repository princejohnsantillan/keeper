<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Gatepasses\Pages;

use App\Filament\Panels\Keeper\Resources\Gatepasses\GatepassResource;
use Filament\Resources\Pages\ListRecords;

final class ListGatepasses extends ListRecords
{
    protected static string $resource = GatepassResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
