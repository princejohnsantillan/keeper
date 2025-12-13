<?php

namespace App\Filament\Guardian\Resources\Children\Pages;

use App\Filament\Guardian\Resources\Children\ChildResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChild extends CreateRecord
{
    protected static string $resource = ChildResource::class;
}
