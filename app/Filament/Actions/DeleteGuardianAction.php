<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\DeleteAction;

final class DeleteGuardianAction
{
    public static function make(?string $name = 'delete'): DeleteAction
    {
        return DeleteAction::make($name)
            ->using(function (Guardian $record): void {
                $childIds = AuthUser::guardian()->children()->pluck('children.id');

                Relationship::query()
                    ->where('guardian_id', $record->id)
                    ->whereIn('child_id', $childIds)
                    ->delete();

                AppNotification::deleted()->send();
            })
            ->visible(fn (Guardian $record) => AuthUser::guardianId() !== $record->id);
    }
}
