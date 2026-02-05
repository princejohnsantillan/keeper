<?php

declare(strict_types=1);

use App\Filament\Panels\Guardian\Resources\Children\Pages\ListChildren;
use App\Models\Child;
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

it('shows children owned by the authenticated user', function () {
    $ownedChild = Child::factory()->create([
        'owner_id' => $this->user->id,
        'first_name' => 'OwnedChild',
    ]);

    Livewire::test(ListChildren::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$ownedChild]);
});

it('does not show children not owned by the authenticated user', function () {
    $otherUser = User::factory()->create();

    $ownedChild = Child::factory()->create([
        'owner_id' => $this->user->id,
        'first_name' => 'OwnedChild',
    ]);

    $notOwnedChild = Child::factory()->create([
        'owner_id' => $otherUser->id,
        'first_name' => 'NotOwnedChild',
    ]);

    Livewire::test(ListChildren::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$ownedChild])
        ->assertCanNotSeeTableRecords([$notOwnedChild]);
});
