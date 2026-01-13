<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\DetachGuardianFromChildrenAction;
use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Models\Guardian;
use Filament\Actions\DeleteAction;

final class DeleteGuardianAction
{
    public static function make(?string $name = 'delete'): DeleteAction
    {
        return DeleteAction::make($name)
            ->using(function (Guardian $record, DetachGuardianFromChildrenAction $detachGuardian): void {
                $childIds = AuthUser::guardian()->children()->pluck('children.id');

                $detachGuardian($record, $childIds);

                AppNotification::deleted()->send();
            })
            ->visible(fn (Guardian $record): bool => AuthUser::guardianId() !== $record->id);
    }
}
