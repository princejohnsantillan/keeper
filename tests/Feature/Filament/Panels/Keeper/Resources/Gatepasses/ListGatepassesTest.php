<?php

declare(strict_types=1);

use App\Filament\Panels\Keeper\Resources\Gatepasses\Pages\ListGatepasses;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('keeper'));

    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    Keeper::factory()->for($this->organization)->for($this->user)->create();

    $this->actingAs($this->user);
});

it('can render the list page', function () {
    Livewire::test(ListGatepasses::class)
        ->assertSuccessful();
});

it('can list gatepasses', function () {
    $activity = Activity::factory()
        ->for($this->organization)
        ->create();

    $gatepasses = Gatepass::factory()
        ->for($activity)
        ->count(3)
        ->create();

    Livewire::test(ListGatepasses::class)
        ->assertCanSeeTableRecords($gatepasses);
});

it('renders guardian avatar column', function () {
    $activity = Activity::factory()
        ->for($this->organization)
        ->create();

    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    $gatepass = Gatepass::factory()
        ->for($activity)
        ->for($guardian)
        ->for($child)
        ->create();

    Livewire::test(ListGatepasses::class)
        ->assertCanSeeTableRecords([$gatepass])
        ->assertTableColumnExists('guardian.avatar')
        ->assertTableColumnExists('guardian.gender')
        ->assertTableColumnExists('guardian.full_name');
});

it('renders child avatar column', function () {
    $activity = Activity::factory()
        ->for($this->organization)
        ->create();

    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    $gatepass = Gatepass::factory()
        ->for($activity)
        ->for($guardian)
        ->for($child)
        ->create();

    Livewire::test(ListGatepasses::class)
        ->assertCanSeeTableRecords([$gatepass])
        ->assertTableColumnExists('child.avatar')
        ->assertTableColumnExists('child.gender')
        ->assertTableColumnExists('child.full_name');
});
