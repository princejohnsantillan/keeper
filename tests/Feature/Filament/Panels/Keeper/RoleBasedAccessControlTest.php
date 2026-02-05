<?php

declare(strict_types=1);

use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Pages\ScanGatepass;
use App\Filament\Panels\Keeper\Resources\Activities\ActivityResource;
use App\Filament\Panels\Keeper\Resources\Activities\Pages\ListActivities;
use App\Filament\Panels\Keeper\Resources\Activities\Pages\ViewAttendance;
use App\Filament\Panels\Keeper\Resources\Children\ChildResource;
use App\Filament\Panels\Keeper\Resources\Children\Pages\ListChildren;
use App\Filament\Panels\Keeper\Resources\Gatepasses\GatepassResource;
use App\Filament\Panels\Keeper\Resources\Gatepasses\Pages\ListGatepasses;
use App\Filament\Panels\Keeper\Resources\Guardians\GuardianResource;
use App\Filament\Panels\Keeper\Resources\Keepers\KeeperResource;
use App\Filament\Panels\Keeper\Resources\Messages\MessageResource;
use App\Filament\Panels\Keeper\Resources\Tags\TagResource;
use App\Filament\Panels\Keeper\Resources\Terms\TermResource;
use App\Models\Activity;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('keeper'));

    $this->organization = Organization::factory()->create();
    $this->adminUser = User::factory()->create();
    $this->gatekeeperUser = User::factory()->create();

    $this->adminKeeper = Keeper::factory()
        ->admin()
        ->for($this->organization)
        ->for($this->adminUser)
        ->create();

    $this->gatekeeperKeeper = Keeper::factory()
        ->gatekeeper()
        ->for($this->organization)
        ->for($this->gatekeeperUser)
        ->create();

    Subdomain::fake($this->organization);
});

describe('Admin role - resource access', function () {
    beforeEach(function () {
        $this->actingAs($this->adminUser);
    });

    it('can access ChildResource', function () {
        expect(ChildResource::canAccess())->toBeTrue();
    });

    it('can access GuardianResource', function () {
        expect(GuardianResource::canAccess())->toBeTrue();
    });

    it('can access TermResource', function () {
        expect(TermResource::canAccess())->toBeTrue();
    });

    it('can access MessageResource', function () {
        expect(MessageResource::canAccess())->toBeTrue();
    });

    it('can access TagResource', function () {
        expect(TagResource::canAccess())->toBeTrue();
    });

    it('can access KeeperResource', function () {
        expect(KeeperResource::canAccess())->toBeTrue();
    });

    it('can access ActivityResource', function () {
        expect(ActivityResource::canAccess())->toBeTrue();
    });

    it('can access GatepassResource', function () {
        expect(GatepassResource::canAccess())->toBeTrue();
    });
});

describe('Gatekeeper role - resource access', function () {
    beforeEach(function () {
        $this->actingAs($this->gatekeeperUser);
    });

    it('cannot access ChildResource', function () {
        expect(ChildResource::canAccess())->toBeFalse();
    });

    it('cannot access GuardianResource', function () {
        expect(GuardianResource::canAccess())->toBeFalse();
    });

    it('cannot access TermResource', function () {
        expect(TermResource::canAccess())->toBeFalse();
    });

    it('cannot access MessageResource', function () {
        expect(MessageResource::canAccess())->toBeFalse();
    });

    it('cannot access TagResource', function () {
        expect(TagResource::canAccess())->toBeFalse();
    });

    it('cannot access KeeperResource', function () {
        expect(KeeperResource::canAccess())->toBeFalse();
    });

    it('can access ActivityResource', function () {
        expect(ActivityResource::canAccess())->toBeTrue();
    });

    it('can access GatepassResource', function () {
        expect(GatepassResource::canAccess())->toBeTrue();
    });
});

describe('Admin role - pages and actions', function () {
    beforeEach(function () {
        $this->actingAs($this->adminUser);
    });

    it('can render ScanGatepass page', function () {
        Livewire::test(ScanGatepass::class)
            ->assertSuccessful();
    });

    it('can render ListActivities page with create action', function () {
        Livewire::test(ListActivities::class)
            ->assertSuccessful()
            ->assertActionExists('create');
    });

    it('can render ViewAttendance page', function () {
        $activity = Activity::factory()->for($this->organization)->create();

        Livewire::test(ViewAttendance::class, ['record' => $activity->id])
            ->assertSuccessful();
    });

    it('can render ListGatepasses page without create action', function () {
        Livewire::test(ListGatepasses::class)
            ->assertSuccessful()
            ->assertActionDoesNotExist('create');
    });

    it('can render ListChildren page without create action', function () {
        Livewire::test(ListChildren::class)
            ->assertSuccessful()
            ->assertActionDoesNotExist('create');
    });
});

describe('Gatekeeper role - pages and actions', function () {
    beforeEach(function () {
        $this->actingAs($this->gatekeeperUser);
    });

    it('can render ScanGatepass page', function () {
        Livewire::test(ScanGatepass::class)
            ->assertSuccessful();
    });

    it('can render ListActivities page without create action', function () {
        Livewire::test(ListActivities::class)
            ->assertSuccessful()
            ->assertActionDoesNotExist('create');
    });

    it('can render ViewAttendance page', function () {
        $activity = Activity::factory()->for($this->organization)->create();

        Livewire::test(ViewAttendance::class, ['record' => $activity->id])
            ->assertSuccessful();
    });

    it('can render ListGatepasses page without create action', function () {
        Livewire::test(ListGatepasses::class)
            ->assertSuccessful()
            ->assertActionDoesNotExist('create');
    });
});

describe('Activity table actions visibility', function () {
    it('hides edit and delete actions from gatekeepers', function () {
        $this->actingAs($this->gatekeeperUser);

        $activity = Activity::factory()->for($this->organization)->create();

        Livewire::test(ListActivities::class)
            ->assertTableActionHidden('edit', $activity)
            ->assertTableActionHidden('delete', $activity);
    });

    it('shows edit and delete actions to admins', function () {
        $this->actingAs($this->adminUser);

        $activity = Activity::factory()->for($this->organization)->create();

        Livewire::test(ListActivities::class)
            ->assertTableActionVisible('edit', $activity)
            ->assertTableActionVisible('delete', $activity);
    });

    it('hides walk-in action from admin', function () {
        $this->actingAs($this->adminUser);

        $activity = Activity::factory()->for($this->organization)->create();

        Livewire::test(ListActivities::class)
            ->assertTableActionHidden('walk_in', $activity);
    });

    it('hides walk-in action from gatekeeper', function () {
        $this->actingAs($this->gatekeeperUser);

        $activity = Activity::factory()->for($this->organization)->create();

        Livewire::test(ListActivities::class)
            ->assertTableActionHidden('walk_in', $activity);
    });

    it('shows attendance action to admin', function () {
        $this->actingAs($this->adminUser);

        $activity = Activity::factory()->for($this->organization)->create();

        Livewire::test(ListActivities::class)
            ->assertTableActionVisible('attendance', $activity);
    });

    it('shows attendance action to gatekeeper', function () {
        $this->actingAs($this->gatekeeperUser);

        $activity = Activity::factory()->for($this->organization)->create();

        Livewire::test(ListActivities::class)
            ->assertTableActionVisible('attendance', $activity);
    });
});
