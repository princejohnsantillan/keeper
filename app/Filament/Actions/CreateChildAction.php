<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\CreateChildAction as CreateChildBusinessAction;
use App\Actions\SyncChildGuardiansAction;
use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Models\Child;
use App\Models\Guardian;
use Filament\Actions\CreateAction;

final class CreateChildAction
{
    public static function make(?string $name = null, string $label = 'Add child'): CreateAction
    {
        return CreateAction::make($name)->label($label)
            ->modalHeading('Add Child')
            ->modalSubmitActionLabel('Add')
            ->createAnother(false)
            ->slideOver()
            ->fillForm(function (): array {
                $guardians = Guardian::query()
                    ->ownedBy(AuthUser::user())
                    ->get();

                return [
                    'guardians' => $guardians
                        ->map(fn (Guardian $guardian): array => [
                            'guardian_id' => $guardian->id,
                            'guardian_name' => $guardian->full_name,
                            'relationship' => null,
                        ])
                        ->all(),
                ];
            })
            ->using(function (
                CreateAction $createAction,
                array $data,
                CreateChildBusinessAction $createChildBusinessAction,
                SyncChildGuardiansAction $syncGuardians,
            ): Child {
                /** @var array<int, array{guardian_id?: string, relationship?: string|null}> $rows */
                $rows = $data['guardians'] ?? [];

                unset($data['guardians']);

                $syncData = collect($rows)
                    ->filter(fn (array $row): bool => ! empty($row['guardian_id']) && ! empty($row['relationship']))
                    ->mapWithKeys(fn (array $row): array => [
                        $row['guardian_id'] => ['relationship' => $row['relationship']],
                    ])
                    ->all();

                if (empty($syncData)) {
                    AppNotification::guardianRelationshipRequired()->send();

                    $createAction->halt(true);
                }

                $child = $createChildBusinessAction($data, AuthUser::user());

                $syncGuardians($child, $syncData);

                return $child;
            });
    }
}
