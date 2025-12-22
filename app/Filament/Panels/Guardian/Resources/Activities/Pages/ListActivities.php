<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Pages;

use App\Filament\Panels\Guardian\Resources\Activities\ActivityResource;
use Filament\Resources\Pages\ListRecords;

final class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;
}
