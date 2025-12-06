<?php

namespace App\Filament\Admin\Resources\Services\Pages;

use App\Filament\Admin\Resources\Services\ServiceResource;
use App\Models\Organization;
use App\Subdomain;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->createAnother(false)
                ->mutateFormDataUsing(function (array $data): array {
                    /** @var Organization $organization */
                    $organization = Subdomain::organization();

                    $data['encryption_key'] = Str::random(32);
                    $data['organization_id'] = $organization->id;
                    $data['created_by'] = auth()->id();

                    return $data;
                }),


        ];
    }
}
