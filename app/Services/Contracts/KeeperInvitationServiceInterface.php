<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\KeeperRole;
use App\Models\Keeper;
use App\Models\KeeperInvitation;
use App\Models\Organization;
use App\Models\User;

interface KeeperInvitationServiceInterface
{
    /**
     * Find or create a user for the invitation.
     */
    public function findOrCreateInvitedUser(string $email, string $name): User;

    /**
     * Check if user has a pending invitation for the organization.
     */
    public function hasPendingInvitation(User $user, Organization $organization): bool;

    /**
     * Check if user is already a keeper for the organization.
     */
    public function isKeeperForOrganization(User $user, Organization $organization): bool;

    /**
     * Create a new keeper invitation.
     */
    public function createInvitation(User $user, Organization $organization, User $invitedBy, KeeperRole $role): KeeperInvitation;

    /**
     * Send invitation email to the user.
     */
    public function sendInvitationEmail(KeeperInvitation $invitation): void;

    /**
     * Find a valid invitation by token.
     */
    public function findValidInvitation(string $token): ?KeeperInvitation;

    /**
     * Accept the invitation and create a keeper record.
     */
    public function acceptInvitation(KeeperInvitation $invitation): Keeper;
}
