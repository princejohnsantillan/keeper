<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses\Pages;

use App\Filament\Panels\Guardian\Resources\Gatepasses\GatepassResource;
use App\Models\Gatepass;
use Filament\Resources\Pages\ViewRecord;

final class ViewGatepass extends ViewRecord
{
    protected static string $resource = GatepassResource::class;

    public function getHeading(): string
    {
        /** @var Gatepass $record */
        $record = $this->getRecord();

        return $record->activity->title;
    }

    public function getSubheading(): string
    {
        /** @var Gatepass $record */
        $record = $this->getRecord();

        return 'Gate Pass: '.$record->code;
    }
}
