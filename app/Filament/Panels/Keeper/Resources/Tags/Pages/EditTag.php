<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Tags\Pages;

use App\Filament\Panels\Keeper\Resources\Tags\TagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditTag extends EditRecord
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
