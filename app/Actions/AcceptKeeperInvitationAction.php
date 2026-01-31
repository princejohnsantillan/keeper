<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\InvalidInvitationException;
use App\Models\Keeper;
use App\Services\Contracts\KeeperInvitationServiceInterface;

final class AcceptKeeperInvitationAction
{
    public function __construct(
        private KeeperInvitationServiceInterface $invitationService,
    ) {}

    /**
     * Accept a keeper invitation and create the keeper record.
     *
     * @throws InvalidInvitationException
     */
    public function __invoke(string $token): Keeper
    {
        $invitation = $this->invitationService->findValidInvitation($token);

        if ($invitation === null) {
            throw new InvalidInvitationException;
        }

        return $this->invitationService->acceptInvitation($invitation);
    }
}
