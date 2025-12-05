<?php

namespace App\Filament\Admin\Resources\Children\Pages;

use App\Filament\Admin\Resources\Children\ChildResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChild extends CreateRecord
{
    protected static string $resource = ChildResource::class;
}
