<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Pages;

use App\Filament\Panels\Guardian\Resources\Children\ChildResource;
use App\Filament\Panels\Guardian\Resources\Children\Tables\ChildrenTable;
use App\Models\Child;
use Filament\Resources\Pages\ViewRecord;

final class ViewChild extends ViewRecord
{
    protected static string $resource = ChildResource::class;

    public function getHeading(): string
    {
        /** @var Child $record */
        $record = $this->getRecord();

        return $record->full_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            ChildrenTable::getEditAction(),
            ChildrenTable::getDeleteAction(),
        ];
    }
}
