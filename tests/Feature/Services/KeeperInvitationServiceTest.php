<?php

declare(strict_types=1);

use App\Enums\KeeperRole;
use App\Enums\KeeperStatus;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\User;
use App\Services\Contracts\KeeperInvitationServiceInterface;

it('creates a pending keeper when invitation is created', function () {
    $service = app(KeeperInvitationServiceInterface::class);
    $organization = Organization::factory()->create();
    $invitedBy = User::factory()->create();

    $invitation = $service->createInvitation(
        user: User::factory()->create(),
        organization: $organization,
        invitedBy: $invitedBy,
        role: KeeperRole::Gatekeeper
    );

    $keeper = Keeper::query()
        ->where('user_id', $invitation->user_id)
        ->where('organization_id', $organization->id)
        ->first();

    expect($keeper)->not->toBeNull();
    expect($keeper->status)->toBe(KeeperStatus::Pending);
    expect($keeper->role)->toBe(KeeperRole::Gatekeeper);
});

it('updates keeper status to active when invitation is accepted', function () {
    $service = app(KeeperInvitationServiceInterface::class);
    $organization = Organization::factory()->create();
    $invitedBy = User::factory()->create();
    $user = User::factory()->create();

    $invitation = $service->createInvitation(
        user: $user,
        organization: $organization,
        invitedBy: $invitedBy,
        role: KeeperRole::Admin
    );

    $keeper = Keeper::query()
        ->where('user_id', $user->id)
        ->where('organization_id', $organization->id)
        ->first();

    expect($keeper->status)->toBe(KeeperStatus::Pending);

    $acceptedKeeper = $service->acceptInvitation($invitation);

    expect($acceptedKeeper->id)->toBe($keeper->id);
    expect($acceptedKeeper->status)->toBe(KeeperStatus::Active);
    expect($invitation->refresh()->isAccepted())->toBeTrue();
});

it('does not consider pending keepers as existing keepers', function () {
    $service = app(KeeperInvitationServiceInterface::class);
    $organization = Organization::factory()->create();
    $user = User::factory()->create();

    Keeper::factory()
        ->for($user)
        ->for($organization)
        ->create(['status' => KeeperStatus::Pending]);

    expect($service->isKeeperForOrganization($user, $organization))->toBeFalse();
});

it('considers active keepers as existing keepers', function () {
    $service = app(KeeperInvitationServiceInterface::class);
    $organization = Organization::factory()->create();
    $user = User::factory()->create();

    Keeper::factory()
        ->for($user)
        ->for($organization)
        ->create(['status' => KeeperStatus::Active]);

    expect($service->isKeeperForOrganization($user, $organization))->toBeTrue();
});
