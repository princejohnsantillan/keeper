<?php

declare(strict_types=1);

namespace App\Http\Controllers\Keeper;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\View\View;

final class PrintAttendanceStickerController extends Controller
{
    public function __invoke(Attendance $attendance): View
    {
        $attendance->load(['child', 'checkinGatepass']);

        return view('keeper.attendance-sticker', [
            'attendance' => $attendance,
            'childName' => $attendance->child->full_name,
            'gatepassCode' => $attendance->checkinGatepass?->code ?? $attendance->attendee_code,
        ]);
    }
}
