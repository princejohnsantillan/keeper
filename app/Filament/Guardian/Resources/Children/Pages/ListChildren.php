<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Children\Pages;

use App\Filament\Guardian\Resources\Children\ChildResource;
use App\Models\Child;
use App\Models\Relationship;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

final class ListChildren extends ListRecords
{
    protected static string $resource = ChildResource::class;

    protected ?\App\Enums\Relationship $relationshipType = null;

    protected function getHeaderActions(): array
    {
        return [
            $this->getCreateAction(),
        ];
    }

    private function getCreateAction(): CreateAction
    {
        return CreateAction::make()
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
                $user = Auth::user();

                $guardian = $user?->guardian;

                if ($guardian !== null) {
                    Relationship::create([
                        'guardian_id' => $guardian->id,
                        'child_id' => $record->id,
                        'relationship' => $this->relationshipType,
                    ]);
                }
            });
    }
}
