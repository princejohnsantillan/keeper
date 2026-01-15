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
        return $this->getGatepass()->activity->title;
    }

    public function getSubheading(): string
    {
        return 'Gate Pass: '.$this->getGatepass()->code;
    }

    private function getGatepass(): Gatepass
    {
        /** @var Gatepass */
        return $this->getRecord();
    }
}
