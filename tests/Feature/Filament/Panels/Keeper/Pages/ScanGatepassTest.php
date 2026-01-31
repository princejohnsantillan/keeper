<?php

declare(strict_types=1);

use App\Filament\Panels\Keeper\Pages\ScanGatepass;
use App\Models\Activity;
use App\Models\Attendance;
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
    $this->keeper = Keeper::factory()->for($this->organization)->for($this->user)->create();

    $this->actingAs($this->user);
});

it('can render the scan gatepass page', function () {
    Livewire::test(ScanGatepass::class)
        ->assertSuccessful();
});

it('shows gatepass details when valid code is entered', function () {
    $activity = Activity::factory()->for($this->organization)->create(['title' => 'Test Activity']);
    $child = Child::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
    $guardian = Guardian::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']);

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'ABCDE',
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'ABCDE')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->assertSet('attendanceStatus', 'not_checked_in');
});

it('shows error notification when invalid code is entered', function () {
    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'INVALID')
        ->call('lookup')
        ->assertNotified('Gatepass not found');
});

it('can check in from the scan page', function () {
    $activity = Activity::factory()->for($this->organization)->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'CHECK',
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'CHECK')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->assertSet('attendanceStatus', 'not_checked_in')
        ->call('checkIn')
        ->assertNotified('Checked in')
        ->assertSet('attendanceStatus', 'checked_in');

    $this->assertDatabaseHas(Attendance::class, [
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checkin_keeper_id' => $this->keeper->id,
    ]);
});

it('can check out from the scan page', function () {
    $activity = Activity::factory()->for($this->organization)->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'CHOUT',
    ]);

    Attendance::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checkin_gatepass_id' => $gatepass->id,
        'checkin_keeper_id' => $this->keeper->id,
        'checked_in_at' => now(),
        'checked_out_at' => null,
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'CHOUT')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->assertSet('attendanceStatus', 'checked_in')
        ->call('checkOut')
        ->assertNotified('Checked out')
        ->assertSet('attendanceStatus', 'checked_out');

    $this->assertDatabaseHas(Attendance::class, [
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checkout_keeper_id' => $this->keeper->id,
    ]);
});

it('shows correct status badge for not checked in', function () {
    $activity = Activity::factory()->for($this->organization)->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'NOTCI',
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'NOTCI')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->assertSet('attendanceStatus', 'not_checked_in');
});

it('shows correct status badge for checked in', function () {
    $activity = Activity::factory()->for($this->organization)->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'CHKIN',
    ]);

    Attendance::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checkin_gatepass_id' => $gatepass->id,
        'checkin_keeper_id' => $this->keeper->id,
        'checked_in_at' => now(),
        'checked_out_at' => null,
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'CHKIN')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->assertSet('attendanceStatus', 'checked_in');
});

it('shows correct status badge for checked out', function () {
    $activity = Activity::factory()->for($this->organization)->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'CHKOT',
    ]);

    Attendance::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checkin_gatepass_id' => $gatepass->id,
        'checkin_keeper_id' => $this->keeper->id,
        'checked_in_at' => now()->subHour(),
        'checkout_keeper_id' => $this->keeper->id,
        'checkout_gatepass_id' => $gatepass->id,
        'checked_out_at' => now(),
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'CHKOT')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->assertSet('attendanceStatus', 'checked_out');
});

it('can clear the gatepass display', function () {
    $activity = Activity::factory()->for($this->organization)->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'CLEAR',
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'CLEAR')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->call('clearGatepass')
        ->assertSet('gatepassId', null)
        ->assertSet('attendanceStatus', null);
});

it('prevents double check-in', function () {
    $activity = Activity::factory()->for($this->organization)->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'DBLCI',
    ]);

    Attendance::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checkin_gatepass_id' => $gatepass->id,
        'checkin_keeper_id' => $this->keeper->id,
        'checked_in_at' => now(),
        'checked_out_at' => null,
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'DBLCI')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->call('checkIn')
        ->assertNotified('Already checked in');
});

it('prevents check out without check in', function () {
    $activity = Activity::factory()->for($this->organization)->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'NOCHI',
    ]);

    Livewire::test(ScanGatepass::class)
        ->set('data.code', 'NOCHI')
        ->call('lookup')
        ->assertSet('gatepassId', $gatepass->id)
        ->call('checkOut')
        ->assertNotified('No check-in found');
});
