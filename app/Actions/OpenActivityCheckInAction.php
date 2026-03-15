<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Activity;

final class OpenActivityCheckInAction
{
    public function __invoke(Activity $activity): Activity
    {
        $activity->update([
            'checkin_opened_at' => now(),
            'checkin_closed_at' => null,
        ]);

        return $activity->refresh();
    }
}
