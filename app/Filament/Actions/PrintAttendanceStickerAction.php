<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Attendance;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class PrintAttendanceStickerAction
{
    public static function make(?string $name = 'print_sticker', string $label = 'Print'): Action
    {
        return Action::make($name)->label($label)
            ->icon(Heroicon::Printer)
            ->color('gray')
            ->url(fn (Attendance $record): string => route('filament.keeper.attendance.print', $record), shouldOpenInNewTab: true);
    }
}
