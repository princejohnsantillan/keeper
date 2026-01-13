<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Keeper;
use App\Subdomain;
use Illuminate\Support\Facades\Auth;

final class GetCurrentKeeperAction
{
    public function __invoke(): Keeper
    {
        $organization = Subdomain::organization();
        $userId = Auth::id();

        $keeper = Keeper::query()
            ->where('organization_id', $organization?->id)
            ->where('user_id', $userId)
            ->first();

        if ($keeper === null) {
            abort(403, 'You are not a keeper for this organization.');
        }

        return $keeper;
    }
}
