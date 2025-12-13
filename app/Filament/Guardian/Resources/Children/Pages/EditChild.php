<?php

namespace App\Filament\Guardian\Resources\Children\Pages;

use App\Filament\Guardian\Resources\Children\ChildResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChild extends EditRecord
{
    protected static string $resource = ChildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
