<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Pages;

use App\Facades\Subdomain;
use App\Filament\Exports\ChildExporter;
use App\Filament\Panels\Keeper\Resources\Children\ChildResource;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

final class ListChildren extends ListRecords
{
    protected static string $resource = ChildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Export children')
                ->exporter(ChildExporter::class)
                ->options([
                    'organization_id' => Subdomain::organization()?->id,
                ]),
        ];
    }
}
