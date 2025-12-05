<?php

namespace App\Filament\Keeper\Resources\Children\Pages;

use App\Filament\Keeper\Resources\Children\ChildResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChild extends CreateRecord
{
    protected static string $resource = ChildResource::class;
}
