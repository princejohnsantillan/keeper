<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\KeeperRole;
use App\Enums\KeeperStatus;
use App\Mail\KeeperInvitationMail;
use App\Models\Keeper;
use App\Models\KeeperInvitation;
use App\Models\Organization;
use App\Models\User;
use App\Services\Contracts\KeeperInvitationServiceInterface;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class KeeperInvitationService implements KeeperInvitationServiceInterface
{
    public function findOrCreateInvitedUser(string $email, string $name): User
    {
        return User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => null,
                'email_verified_at' => null,
            ]
        );
    }

    public function hasPendingInvitation(User $user, Organization $organization): bool
    {
        return KeeperInvitation::query()
            ->where('user_id', $user->id)
            ->where('organization_id', $organization->id)
            ->valid()
            ->exists();
    }

    public function isKeeperForOrganization(User $user, Organization $organization): bool
    {
        return Keeper::query()
            ->where('user_id', $user->id)
            ->where('organization_id', $organization->id)
            ->where('status', '!=', KeeperStatus::Pending)
            ->exists();
    }

    public function createInvitation(User $user, Organization $organization, User $invitedBy, KeeperRole $role): KeeperInvitation
    {
        $token = $this->generateUniqueToken();

        // Create or update a pending Keeper record (handles expired invitations and soft-deleted keepers)
        Keeper::withTrashed()->updateOrCreate(
            [
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ],
            [
                'role' => $role,
                'status' => KeeperStatus::Pending,
                'deleted_at' => null,
            ],
        );

        return KeeperInvitation::query()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'invited_by' => $invitedBy->id,
            'role' => $role,
            'token' => $token,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function sendInvitationEmail(KeeperInvitation $invitation): void
    {
        Mail::to($invitation->user->email)->queue(new KeeperInvitationMail($invitation));
    }

    public function findValidInvitation(string $token): ?KeeperInvitation
    {
        return KeeperInvitation::query()
            ->where('token', $token)
            ->valid()
            ->first();
    }

    public function acceptInvitation(KeeperInvitation $invitation): Keeper
    {
        // Find and update the pending Keeper record
        $keeper = Keeper::query()
            ->where('user_id', $invitation->user_id)
            ->where('organization_id', $invitation->organization_id)
            ->where('status', KeeperStatus::Pending)
            ->firstOrFail();

        $keeper->status = KeeperStatus::Active;
        $keeper->save();

        $invitation->accept();

        return $keeper;
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (KeeperInvitation::query()->where('token', $token)->exists());

        return $token;
    }
}
