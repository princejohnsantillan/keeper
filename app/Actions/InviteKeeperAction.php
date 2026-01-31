<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\KeeperRole;
use App\Exceptions\InvitationAlreadyExistsException;
use App\Exceptions\KeeperAlreadyExistsException;
use App\Models\KeeperInvitation;
use App\Models\Organization;
use App\Models\User;
use App\Services\Contracts\KeeperInvitationServiceInterface;

final class InviteKeeperAction
{
    public function __construct(
        private KeeperInvitationServiceInterface $invitationService,
    ) {}

    /**
     * Invite a user to become a keeper for an organization.
     *
     * @throws KeeperAlreadyExistsException
     * @throws InvitationAlreadyExistsException
     */
    public function __invoke(
        string $email,
        string $name,
        Organization $organization,
        User $invitedBy,
        KeeperRole $role = KeeperRole::Gatekeeper,
    ): KeeperInvitation {
        $user = $this->invitationService->findOrCreateInvitedUser($email, $name);

        if ($this->invitationService->isKeeperForOrganization($user, $organization)) {
            throw new KeeperAlreadyExistsException($email);
        }

        if ($this->invitationService->hasPendingInvitation($user, $organization)) {
            throw new InvitationAlreadyExistsException($email);
        }

        $invitation = $this->invitationService->createInvitation($user, $organization, $invitedBy, $role);

        $this->invitationService->sendInvitationEmail($invitation);

        return $invitation;
    }
}
