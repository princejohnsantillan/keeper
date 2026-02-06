<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\KeeperRole;
use App\Enums\KeeperStatus;
use App\Exceptions\CannotChangeOwnAdminRoleException;
use App\Exceptions\CannotDemoteLastAdminException;
use App\Mail\KeeperRoleChangedMail;
use App\Models\Keeper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class ChangeKeeperRoleAction
{
    /**
     * @throws AuthorizationException
     * @throws CannotDemoteLastAdminException
     * @throws CannotChangeOwnAdminRoleException
     */
    public function __invoke(Keeper $keeper, KeeperRole|string $newRole, Keeper $actingKeeper): Keeper
    {
        if (! $actingKeeper->isAdmin()) {
            throw new AuthorizationException('Only admins can change team member roles.');
        }

        if ($keeper->organization_id !== $actingKeeper->organization_id) {
            throw new AuthorizationException('You can only change roles within your organization.');
        }

        if (! in_array($keeper->status, [KeeperStatus::Active, KeeperStatus::Inactive], true)) {
            throw new AuthorizationException('You can only change roles for active or inactive team members.');
        }

        $normalizedRole = $newRole instanceof KeeperRole
            ? $newRole
            : KeeperRole::tryFrom($newRole);

        if ($normalizedRole === null) {
            throw new AuthorizationException('Invalid role selected.');
        }

        if ($actingKeeper->id === $keeper->id && $normalizedRole !== KeeperRole::Admin) {
            throw new CannotChangeOwnAdminRoleException();
        }

        if ($keeper->role === KeeperRole::Admin && $normalizedRole !== KeeperRole::Admin) {
            $adminCount = Keeper::query()
                ->where('organization_id', $keeper->organization_id)
                ->where('role', KeeperRole::Admin->value)
                ->whereIn('status', [KeeperStatus::Active->value, KeeperStatus::Inactive->value])
                ->count();

            if ($adminCount === 1) {
                throw new CannotDemoteLastAdminException();
            }
        }

        if ($keeper->role === $normalizedRole) {
            return $keeper;
        }

        $previousRole = $keeper->role?->getLabel() ?? 'Unknown';

        $keeper->role = $normalizedRole;
        $keeper->save();

        $keeper->loadMissing(['user', 'organization']);
        $actingKeeper->loadMissing(['user']);

        try {
            Mail::to($keeper->user->email)->queue(new KeeperRoleChangedMail(
                keeper: $keeper,
                previousRole: $previousRole,
                newRole: $normalizedRole->getLabel(),
                changedByName: $actingKeeper->user->name,
            ));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $keeper->refresh();
    }
}
