<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\DetachChildFromGuardianAction;
use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Filament\Panels\Guardian\Resources\Children\ChildResource;
use App\Models\Child;
use Filament\Actions\DeleteAction;

final class DeleteChildAction
{
    public static function make(?string $name = 'delete'): DeleteAction
    {
        return DeleteAction::make($name)
            ->using(function (Child $record, DetachChildFromGuardianAction $detachChild): bool {
                $guardian = AuthUser::guardian();

                $detachChild($record, $guardian);

                return true;
            })
            ->successNotification(AppNotification::deleted())
            ->successRedirectUrl(ChildResource::getUrl('index'));
    }
}
