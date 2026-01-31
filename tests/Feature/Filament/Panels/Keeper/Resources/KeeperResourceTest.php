<?php

declare(strict_types=1);

use App\Enums\KeeperStatus;
use App\Filament\Panels\Keeper\Resources\Keepers\KeeperResource;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\User;

it('uses the correct navigation group', function () {
    expect(KeeperResource::getNavigationGroup())->toBe('Settings');
});

it('has correct status helper methods for active keepers', function () {
    $organization = Organization::factory()->create();
    $activeKeeper = Keeper::factory()
        ->for($organization)
        ->for(User::factory()->create())
        ->create(['status' => KeeperStatus::Active]);

    expect($activeKeeper->isActive())->toBeTrue();
    expect($activeKeeper->isInactive())->toBeFalse();
    expect($activeKeeper->isPending())->toBeFalse();
});

it('has correct status helper methods for inactive keepers', function () {
    $organization = Organization::factory()->create();
    $inactiveKeeper = Keeper::factory()
        ->for($organization)
        ->for(User::factory()->create())
        ->create(['status' => KeeperStatus::Inactive]);

    expect($inactiveKeeper->isActive())->toBeFalse();
    expect($inactiveKeeper->isInactive())->toBeTrue();
    expect($inactiveKeeper->isPending())->toBeFalse();
});

it('has correct status helper methods for pending keepers', function () {
    $organization = Organization::factory()->create();
    $pendingKeeper = Keeper::factory()
        ->for($organization)
        ->for(User::factory()->create())
        ->create(['status' => KeeperStatus::Pending]);

    expect($pendingKeeper->isActive())->toBeFalse();
    expect($pendingKeeper->isInactive())->toBeFalse();
    expect($pendingKeeper->isPending())->toBeTrue();
});

it('casts status to KeeperStatus enum', function () {
    $organization = Organization::factory()->create();
    $keeper = Keeper::factory()
        ->for($organization)
        ->for(User::factory()->create())
        ->create(['status' => 'active']);

    expect($keeper->status)->toBeInstanceOf(KeeperStatus::class);
    expect($keeper->status)->toBe(KeeperStatus::Active);
});

it('defaults to active status', function () {
    $organization = Organization::factory()->create();
    $keeper = Keeper::factory()
        ->for($organization)
        ->for(User::factory()->create())
        ->create();

    expect($keeper->status)->toBe(KeeperStatus::Active);
});

it('can create a keeper with pending status for invitations', function () {
    $organization = Organization::factory()->create();
    $keeper = Keeper::factory()
        ->for($organization)
        ->for(User::factory()->create())
        ->create(['status' => KeeperStatus::Pending]);

    expect($keeper->status)->toBe(KeeperStatus::Pending);
    expect($keeper->isPending())->toBeTrue();
});
