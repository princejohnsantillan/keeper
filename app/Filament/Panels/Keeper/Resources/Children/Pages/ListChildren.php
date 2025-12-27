<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Pages;

use App\Filament\Panels\Keeper\Resources\Children\ChildResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListChildren extends ListRecords
{
    protected static string $resource = ChildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
