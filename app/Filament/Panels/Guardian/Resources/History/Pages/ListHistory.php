<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\History\Pages;

use App\Filament\Panels\Guardian\Resources\History\HistoryResource;
use Filament\Resources\Pages\ListRecords;

final class ListHistory extends ListRecords
{
    protected static string $resource = HistoryResource::class;
}
