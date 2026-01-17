<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Pages;

use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Filament\Panels\Guardian\Resources\Guardians\GuardianResource;
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
                ->using(function (CreateAction $action, array $data): Guardian {
                    /** @var array<int, array{child_id?: string, relationship?: string|null}> $rows */
                    $rows = $data['children'] ?? [];

                    unset($data['children']);

                    $data['owner_id'] = AuthUser::userId();

                    $guardian = Guardian::query()->create($data);

                    $syncData = collect($rows)
                        ->filter(fn (array $row): bool => ! empty($row['child_id']) && ! empty($row['relationship']))
                        ->mapWithKeys(fn (array $row): array => [
                            $row['child_id'] => ['relationship' => $row['relationship']],
                        ])
                        ->all();

                    if (empty($syncData)) {
                        AppNotification::childRelationshipRequired()->send();

                        $action->halt(true);
                    }

                    $guardian->children()->sync($syncData);

                    return $guardian;
                }),
        ];
    }
}
