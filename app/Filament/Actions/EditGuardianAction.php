<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\UpdateGuardianAction;
use App\AuthUser;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\EditAction;

final class EditGuardianAction
{
    public static function make(?string $name = 'edit'): EditAction
    {
        return EditAction::make($name)
            ->slideOver()
            ->mutateRecordDataUsing(function (array $data, Guardian $record): array {
                $children = AuthUser::guardian()->children;

                $relationshipsByChildId = Relationship::query()
                    ->where('guardian_id', $record->id)
                    ->whereIn('child_id', $children->pluck('id'))
                    ->get()
                    ->keyBy('child_id');

                $data['children'] = $children
                    ->map(fn (Child $child): array => [
                        'child_id' => $child->id,
                        'child_name' => $child->full_name,
                        'relationship' => $relationshipsByChildId->get($child->id)?->relationship?->value,
                    ])
                    ->all();

                return $data;
            })
            ->using(function (Guardian $record, array $data, UpdateGuardianAction $updateGuardian): Guardian {
                /** @var array<int, array{child_id?: int|string, relationship?: string|null}> $rows */
                $rows = $data['children'] ?? [];

                unset($data['children']);

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

                return $updateGuardian($record, $data, $syncData);
            });
    }
}
