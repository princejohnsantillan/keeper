<?php

namespace App\Filament\Keeper\Resources\Children\Pages;

use App\Filament\Keeper\Resources\Children\ChildResource;
use App\Models\Child;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListChildren extends ListRecords
{
    protected static string $resource = ChildResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->after(function (Child $record, array $data): void {
                    /** @var \App\Models\User $user */
                    $user = Auth::user();

                    $user->keeper->children()->attach($record->id, [
                        'relationship' => $data['relationship'],
                    ]);
                }),
        ];
    }
}
