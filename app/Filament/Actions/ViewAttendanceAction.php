<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Panels\Keeper\Resources\Activities\ActivityResource;
use App\Models\Activity;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class ViewAttendanceAction
{
    public static function make(?string $name = 'attendance', string $label = 'Attendance'): Action
    {
        return Action::make($name)->label($label)
            ->icon(Heroicon::ClipboardDocumentCheck)
            ->color('gray')
            ->url(fn (Activity $record): string => ActivityResource::getUrl('attendance', ['record' => $record]));
    }
}
