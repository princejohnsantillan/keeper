<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\UpdateGuardianAction;
use App\Facades\Subdomain;
use App\Filament\Notifications\AppNotification;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\EditAction;

final class KeeperEditGuardianAction
{
    public static function make(?string $name = 'edit'): EditAction
    {
        return EditAction::make($name)
            ->slideOver()
            ->visible(function (Guardian $record): bool {
                $organization = Subdomain::organization();

                if ($organization === null) {
                    return false;
                }

                return $record->ownerships()
                    ->where('owner_type', $organization->getMorphClass())
                    ->where('owner_id', $organization->getKey())
                    ->exists();
            })
            ->mutateRecordDataUsing(function (array $data, Guardian $record): array {
                $organization = Subdomain::organization();

                $children = Child::query()
                    ->ownedBy($organization)
                    ->get();

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
            ->using(function (
                EditAction $editAction,
                Guardian $record,
                array $data,
                UpdateGuardianAction $updateGuardian,
            ): Guardian {
                $organization = Subdomain::organization();

                abort_unless(
                    $organization !== null && $record->ownerships()
                        ->where('owner_type', $organization->getMorphClass())
                        ->where('owner_id', $organization->getKey())
                        ->exists(),
                    403,
                );

                /** @var array<int, array{child_id?: string, relationship?: string|null}> $rows */
                $rows = $data['children'] ?? [];

                unset($data['children']);

                $allowedChildIds = Child::query()
                    ->ownedBy($organization)
                    ->pluck('id');

                $syncData = collect($rows)
                    ->filter(fn (array $row): bool => ! empty($row['child_id']) && ! empty($row['relationship']))
                    ->filter(fn (array $row): bool => $allowedChildIds->contains($row['child_id']))
                    ->mapWithKeys(fn (array $row): array => [
                        $row['child_id'] => ['relationship' => $row['relationship']],
                    ])
                    ->all();

                if (empty($syncData)) {
                    AppNotification::childRelationshipRequired()->send();

                    $editAction->halt(true);
                }

                return $updateGuardian($record, $data, $syncData);
            });
    }
}
