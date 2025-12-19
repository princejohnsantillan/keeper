<?php

namespace App\Filament\Guardian\Resources\Children\Pages;

use App\Filament\Guardian\Resources\Children\ChildResource;
use App\Models\Child;
use App\Models\Relationship;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListChildren extends ListRecords
{
    protected static string $resource = ChildResource::class;

    protected ?\App\Enums\Relationship $relationshipType = null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->label('Add child')
                ->modalHeading('Add Child')
                ->modalSubmitActionLabel('Add')
                ->createAnother(false)
                ->mutateDataUsing(function (array $data): array {

                    $this->relationshipType = $data['relationship'];
                    unset($data['relationship']);

                    return $data;
                })
                ->after(function (Child $record): void {
                    /** @var \App\Models\User $user */
                    $user = Auth::user();

                    Relationship::create([
                        'guardian_id' => $user->guardian->id,
                        'child_id' => $record->id,
                        'relationship' => $this->relationshipType,
                    ]);
                }),
        ];
    }
}
