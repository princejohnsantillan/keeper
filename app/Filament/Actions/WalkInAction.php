<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Activity;
use Filament\Actions\Action;

final class WalkInAction
{
    public static function make(?string $name = 'walk_in', string $label = 'Walk-in'): Action
    {
        return Action::make($name)->label($label)
            ->icon('heroicon-o-user-plus')
            ->color('success')
            ->url(fn (Activity $record): string => route('keeper.walk-ins.create', ['activity' => $record]));
    }
}
