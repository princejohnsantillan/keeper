<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Activity;
use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;

final class WalkInAction
{
    public static function make(?string $name = 'walk_in', string $label = 'Walk-in'): Action
    {
        return Action::make($name)->label($label)
            ->icon('heroicon-o-user-plus')
            ->color('success')
            ->url(fn (Activity $record): string => URL::temporarySignedRoute(
                'filament.keeper.pages.walk-in-registration',
                now()->addHours(24),
                ['activity' => $record->id],
            ))
            ->openUrlInNewTab();
    }
}
