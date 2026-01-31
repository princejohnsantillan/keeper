<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\KeeperRole;
use App\Models\Keeper;
use App\Models\Organization;

final class OrganizationObserver
{
    /**
     * Handle the Organization "created" event.
     */
    public function created(Organization $organization): void
    {
        // Automatically create an Admin keeper for the organization owner
        if ($organization->owner_id) {
            Keeper::query()->create([
                'organization_id' => $organization->id,
                'user_id' => $organization->owner_id,
                'role' => KeeperRole::Admin,
            ]);
        }
    }
}
