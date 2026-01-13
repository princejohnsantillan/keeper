<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Models\Child;
use App\Models\Relationship;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

final class DeleteChildAction
{
    public static function make(?string $name = 'delete'): DeleteAction
    {
        return DeleteAction::make($name)
            ->using(function (Child $record): void {
                Relationship::where('child_id', $record->id)
                    ->where('guardian_id', AuthUser::guardianId())
                    ->delete();

                Notification::make()
                    ->title('Deleted')
                    ->danger()
                    ->send();
            });
    }
}
