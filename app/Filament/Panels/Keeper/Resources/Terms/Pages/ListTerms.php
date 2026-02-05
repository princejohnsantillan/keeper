<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Terms\Pages;

use App\AuthUser;
use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Resources\Terms\TermResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListTerms extends ListRecords
{
    protected static string $resource = TermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->createAnother(false)
                ->mutateDataUsing(function (array $data): array {
                    $data['organization_id'] = Subdomain::organization()?->id;
                    $data['created_by'] = AuthUser::user()->id;

                    return $data;
                }),
        ];
    }
}
