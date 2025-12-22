<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Pages;

use App\Filament\Panels\Keeper\Resources\Activities\ActivityResource;
use App\Subdomain;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->label('Add activity')
                ->modalHeading('Add activity')
                ->createAnother(false)
                ->modalSubmitActionLabel('Add')
                ->mutateDataUsing(function (array $data): array {
                    $data['organization_id'] = Subdomain::organization()?->id;
                    $data['created_by'] = auth()->id();

                    return $data;
                }),
        ];
    }
}
