<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Messages\Pages;

use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Resources\Messages\MessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->createAnother(false)
                ->mutateDataUsing(function (array $data): array {
                    $data['organization_id'] = Subdomain::organization()?->id;

                    return $data;
                }),
        ];
    }
}
