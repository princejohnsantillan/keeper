<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\History\Pages;

use App\Filament\Panels\Guardian\Resources\History\HistoryResource;
use App\Models\Attendance;
use Filament\Resources\Pages\ViewRecord;

final class ViewHistory extends ViewRecord
{
    protected static string $resource = HistoryResource::class;

    public function getHeading(): string
    {
        return $this->getAttendance()->activity->title;
    }

    public function getSubheading(): string
    {
        return $this->getAttendance()->child->full_name;
    }

    private function getAttendance(): Attendance
    {
        /** @var Attendance */
        return $this->getRecord();
    }
}
