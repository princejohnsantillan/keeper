<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Activity;

final class CloseActivityCheckInAction
{
    public function __invoke(Activity $activity): Activity
    {
        if (! $activity->hasCheckInOpened()) {
            return $activity;
        }

        $activity->update([
            'checkin_closed_at' => now(),
        ]);

        return $activity->refresh();
    }
}
