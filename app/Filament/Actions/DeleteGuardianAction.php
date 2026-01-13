<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

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

                Notification::make()
                    ->title('Deleted')
                    ->danger()
                    ->send();
            })
            ->visible(fn (Guardian $record) => AuthUser::guardianId() !== $record->id);
    }
}
