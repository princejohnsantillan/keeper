<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\InvitationCreated;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Invitation;
use App\Models\Organization;
use App\ReadableCode;
use App\Services\Contracts\InvitationServiceInterface;
use Illuminate\Support\Facades\Mail;

final class InvitationService implements InvitationServiceInterface
{
    public function generateUniqueCode(Organization $organization): string
    {
        do {
            $code = ReadableCode::generate();
        } while (Invitation::query()
            ->where('organization_id', $organization->id)
            ->where('code', $code)
            ->exists());

        return $code;
    }

    public function findValidForRegistration(string $code, Activity $activity, Child $child): ?Invitation
    {
        return Invitation::query()
            ->where('code', $code)
            ->where('activity_id', $activity->id)
            ->where('organization_id', $activity->organization_id)
            ->where(function ($query) use ($child): void {
                $query->whereNull('used_on_child_id')
                    ->orWhere('used_on_child_id', $child->id);
            })
            ->first();
    }

    public function markAsUsed(Invitation $invitation, Child $child): void
    {
        $invitation->used_on_child_id = $child->id;
        $invitation->save();
    }

    public function sendInvitationEmail(Invitation $invitation): void
    {
        $email = $invitation->invitee_email;

        if (trim($email) === '') {
            return;
        }

        Mail::to($email)->queue(new InvitationCreated($invitation));
    }
}
