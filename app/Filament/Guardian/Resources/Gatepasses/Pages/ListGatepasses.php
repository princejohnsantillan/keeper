<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Gatepasses\Pages;

use App\Filament\Guardian\Resources\Gatepasses\GatepassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListGatepasses extends ListRecords
{
    protected static string $resource = GatepassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->slideOver(),
        ];
    }
}
