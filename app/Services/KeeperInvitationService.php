<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\KeeperRole;
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
            ->exists();
    }

    public function createInvitation(User $user, Organization $organization, User $invitedBy, KeeperRole $role): KeeperInvitation
    {
        $token = $this->generateUniqueToken();

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
        $keeper = Keeper::query()->create([
            'user_id' => $invitation->user_id,
            'organization_id' => $invitation->organization_id,
            'role' => $invitation->role,
        ]);

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
