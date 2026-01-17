<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\SyncChildGuardiansAction;
use App\Actions\UpdateChildAction;
use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\EditAction;

final class EditChildAction
{
    public static function make(?string $name = 'edit'): EditAction
    {
        return EditAction::make($name)
            ->slideOver()
            ->mutateRecordDataUsing(function (array $data, Child $record): array {
                $guardians = Guardian::query()
                    ->where('owner_id', AuthUser::userId())
                    ->get();

                $relationshipsByGuardianId = Relationship::query()
                    ->where('child_id', $record->id)
                    ->whereIn('guardian_id', $guardians->pluck('id'))
                    ->get()
                    ->keyBy('guardian_id');

                $data['guardians'] = $guardians
                    ->map(fn (Guardian $guardian): array => [
                        'guardian_id' => $guardian->id,
                        'guardian_name' => $guardian->full_name,
                        'relationship' => $relationshipsByGuardianId->get($guardian->id)?->relationship?->value,
                    ])
                    ->all();

                return $data;
            })
            ->using(function (
                EditAction $editAction,
                Child $record,
                array $data,
                UpdateChildAction $updateChild,
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

                    $editAction->halt(true);
                }

                $child = $updateChild($record, $data);

                $syncGuardians($child, $syncData);

                return $child;
            });
    }
}
