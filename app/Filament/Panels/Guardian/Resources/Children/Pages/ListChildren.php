<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Pages;

use App\Filament\Actions\CreateChildAction;
use App\Filament\Panels\Guardian\Resources\Children\ChildResource;
use Filament\Resources\Pages\ListRecords;

final class ListChildren extends ListRecords
{
    protected static string $resource = ChildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateChildAction::make(),
        ];
    }
}
