<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Activity;
use App\Models\Child;
use App\Models\Invitation;
use App\Models\Organization;

interface InvitationServiceInterface
{
    /**
     * Generate a unique invitation code within an organization.
     */
    public function generateUniqueCode(Organization $organization): string;

    /**
     * Find a valid invitation for registration.
     *
     * Returns the invitation if the code is valid for the activity and either
     * unused or already used for the same child (allowing different guardians).
     */
    public function findValidForRegistration(string $code, Activity $activity, Child $child): ?Invitation;

    /**
     * Mark an invitation as used by a specific child.
     */
    public function markAsUsed(Invitation $invitation, Child $child): void;

    /**
     * Send the invitation email to the invitee.
     */
    public function sendInvitationEmail(Invitation $invitation): void;
}
