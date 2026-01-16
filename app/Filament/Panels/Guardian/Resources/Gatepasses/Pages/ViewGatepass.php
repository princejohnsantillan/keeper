<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses\Pages;

use App\Filament\Panels\Guardian\Resources\Gatepasses\GatepassResource;
use Filament\Resources\Pages\ViewRecord;

final class ViewGatepass extends ViewRecord
{
    protected static string $resource = GatepassResource::class;

    public function getHeading(): string
    {
        return '';
    }
}
