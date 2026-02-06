<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Keeper;
use App\Models\KeeperInvitation;

final class CancelKeeperInvitationAction
{
    public function __invoke(Keeper $keeper): void
    {
        KeeperInvitation::query()
            ->where('user_id', $keeper->user_id)
            ->where('organization_id', $keeper->organization_id)
            ->delete();

        $user = $keeper->user;

        if ($user->email_verified_at === null) {
            $keeper->forceDelete();
            $user->delete();
        } else {
            $keeper->delete();
        }
    }
}
