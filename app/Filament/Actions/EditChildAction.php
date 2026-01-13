<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
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
                $relationship = Relationship::where('child_id', $record->id)
                    ->whereNotNull('guardian_id')
                    ->where('guardian_id', AuthUser::guardianId())
                    ->first();

                $data['relationship'] = $relationship?->relationship?->value;

                return $data;
            })
            ->using(function (Child $record, array $data): Child {
                $relationship = $data['relationship'];

                unset($data['relationship']);

                $record->update($data);

                Relationship::where('child_id', $record->id)
                    ->where('guardian_id', AuthUser::guardianId())
                    ->whereNotNull('guardian_id')
                    ->update(['relationship' => $relationship]);

                return $record;
            });
    }
}
