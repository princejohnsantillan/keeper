<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Pages;

use App\Filament\Panels\Keeper\Resources\Children\ChildResource;
use App\Filament\Panels\Keeper\Resources\Children\Tables\ChildrenTable;
use Filament\Resources\Pages\ViewRecord;

final class ViewChild extends ViewRecord
{
    protected static string $resource = ChildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChildrenTable::getEditAction(),
        ];
    }
}
