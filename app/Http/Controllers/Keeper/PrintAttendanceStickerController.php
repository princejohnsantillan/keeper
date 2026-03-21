<?php

declare(strict_types=1);

namespace App\Http\Controllers\Keeper;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class PrintAttendanceStickerController extends Controller
{
    public function __invoke(Attendance $attendance): View
    {
        $attendance->load(['child.organizationTags', 'checkinGatepass']);

        $child = $attendance->child;
        $childTags = $child->organizationTags
            ->pluck('name')
            ->filter(static fn (?string $tag): bool => $tag !== null && trim($tag) !== '')
            ->take(4)
            ->values()
            ->all();
        $guardianNote = Str::limit(Str::squish((string) $child->notes), 50, '...');
        $checkinCode = (string) ($attendance->checkinGatepass?->code ?? '-');

        return view('keeper.attendance-sticker', [
            'childKnownAs' => Str::upper($child->known_as),
            'childLastName' => $child->last_name,
            'checkinCode' => $checkinCode,
            'childTags' => $childTags,
            'guardianNote' => $guardianNote,
        ]);
    }
}
