<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Tags\Pages;

use App\Filament\Panels\Keeper\Resources\Tags\TagResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;
}
