<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Attendance;
use App\Models\Gatepass;

final class IsCheckedInAction
{
    public function __invoke(Gatepass $gatepass): bool
    {
        return Attendance::query()
            ->where('activity_id', $gatepass->activity_id)
            ->where('child_id', $gatepass->child_id)
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->exists();
    }
}
