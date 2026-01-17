<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Panels\Guardian\Resources\Activities\ActivityResource;
use App\Models\Activity;
use Filament\Actions\Action;

final class AttendActivityAction
{
    public static function make(?string $name = 'register_activity', string $label = 'Register'): Action
    {
        return Action::make($name)
            ->label($label)
            ->button()
            ->url(fn (Activity $record): string => ActivityResource::getUrl('attend', ['record' => $record]));
    }
}
