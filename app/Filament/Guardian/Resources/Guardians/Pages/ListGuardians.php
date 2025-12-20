<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Guardians\Pages;

use App\AuthUser;
use App\Filament\Guardian\Resources\Guardians\GuardianResource;
use App\Models\Child;
use App\Models\Guardian;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListGuardians extends ListRecords
{
    protected static string $resource = GuardianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add guardian')
                ->modalSubmitActionLabel('Add')
                ->createAnother(false)
                ->slideOver()
                ->fillForm(function (): array {
                    $children = AuthUser::guardian()->children;

                    return [
                        'children' => $children
                            ->map(fn (Child $child): array => [
                                'child_id' => $child->id,
                                'child_name' => $child->full_name,
                                'relationship' => null,
                            ])
                            ->all(),
                    ];
                })
                ->using(function (array $data): Guardian {
                    /** @var array<int, array{child_id?: int|string, relationship?: string|null}> $rows */
                    $rows = $data['children'] ?? [];

                    unset($data['children']);

                    $guardian = Guardian::create($data);

                    $syncData = [];

                    foreach ($rows as $row) {
                        $childId = (int) ($row['child_id'] ?? 0);
                        $relationship = $row['relationship'] ?? null;

                        if ($childId <= 0) {
                            continue;
                        }

                        if ($relationship === null || $relationship === '') {
                            continue;
                        }

                        $syncData[$childId] = [
                            'relationship' => $relationship,
                        ];
                    }

                    $guardian->children()->sync($syncData);

                    return $guardian;
                }),
        ];
    }
}
