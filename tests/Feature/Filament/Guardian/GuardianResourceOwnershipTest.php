<?php

declare(strict_types=1);

use App\Filament\Panels\Guardian\Resources\Guardians\Pages\ListGuardians;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('guardian'));

    $organization = Organization::factory()->create(['slug' => 'test-org']);
    $guardian = Guardian::factory()->create();
    $user = User::factory()->create(['guardian_id' => $guardian->id]);

    Config::set('app.domain', 'keeper.test');

    $this->withServerVariables(['HTTP_HOST' => 'test-org.keeper.test']);

    $this->actingAs($user);

    $this->user = $user;
    $this->guardian = $guardian;
    $this->organization = $organization;
});

it('shows guardians owned by the authenticated user', function () {
    $ownedGuardian = Guardian::factory()->create([
        'owner_id' => $this->user->id,
        'first_name' => 'OwnedGuardian',
    ]);

    Livewire::test(ListGuardians::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$ownedGuardian]);
});

it('does not show guardians not owned by the authenticated user', function () {
    $otherUser = User::factory()->create();

    $ownedGuardian = Guardian::factory()->create([
        'owner_id' => $this->user->id,
        'first_name' => 'OwnedGuardian',
    ]);

    $notOwnedGuardian = Guardian::factory()->create([
        'owner_id' => $otherUser->id,
        'first_name' => 'NotOwnedGuardian',
    ]);

    Livewire::test(ListGuardians::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$ownedGuardian])
        ->assertCanNotSeeTableRecords([$notOwnedGuardian]);
});
