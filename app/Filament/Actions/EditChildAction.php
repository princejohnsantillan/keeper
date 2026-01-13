<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\UpdateChildAction;
use App\AuthUser;
use App\Enums\Relationship as RelationshipEnum;
use App\Models\Child;
use App\Models\Relationship;
use Filament\Actions\EditAction;

final class EditChildAction
{
    public static function make(?string $name = 'edit'): EditAction
    {
        return EditAction::make($name)
            ->slideOver()
            ->mutateRecordDataUsing(function (array $data, Child $record): array {
                $relationship = Relationship::query()
                    ->where('child_id', $record->id)
                    ->whereNotNull('guardian_id')
                    ->where('guardian_id', AuthUser::guardianId())
                    ->first();

                $data['relationship'] = $relationship?->relationship?->value;

                return $data;
            })
            ->using(function (Child $record, array $data, UpdateChildAction $updateChild): Child {
                $relationshipValue = $data['relationship'] ?? null;
                unset($data['relationship']);

                $guardian = AuthUser::guardian();
                $relationship = $relationshipValue !== null
                    ? RelationshipEnum::from($relationshipValue)
                    : null;

                return $updateChild($record, $data, $guardian, $relationship);
            });
    }
}
