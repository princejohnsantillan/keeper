<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Attendance;

final class RecordAttendanceStickerPrintedAction
{
    public function __invoke(Attendance $attendance): Attendance
    {
        $attendance->update(['last_printed_at' => now()]);

        return $attendance;
    }
}
