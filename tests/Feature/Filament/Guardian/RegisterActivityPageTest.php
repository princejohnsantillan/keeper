<?php

declare(strict_types=1);

use App\Filament\Panels\Guardian\Resources\Activities\Pages\RegisterActivity;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\Relationship;
use App\Models\Term;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
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

    $this->organization = $organization;
    $this->guardian = $guardian;
});

it('shows description when activity has a description', function () {
    $activity = Activity::factory()->for($this->organization)->create([
        'description' => 'This is a test description for the activity.',
        'publish_at' => now(),
    ]);

    Livewire::test(RegisterActivity::class, ['record' => $activity->id])
        ->assertSuccessful()
        ->assertSee('This is a test description for the activity.');
});

it('does not show description placeholder when activity has no description', function () {
    $activity = Activity::factory()->for($this->organization)->create([
        'description' => null,
        'publish_at' => now(),
    ]);

    Livewire::test(RegisterActivity::class, ['record' => $activity->id])
        ->assertSuccessful();
});

it('shows description before terms and conditions', function () {
    $term = Term::factory()->for($this->organization)->published()->create([
        'content' => 'These are the terms and conditions.',
    ]);

    $activity = Activity::factory()->for($this->organization)->create([
        'description' => 'Activity description here.',
        'term_id' => $term->id,
        'publish_at' => now(),
    ]);

    Livewire::test(RegisterActivity::class, ['record' => $activity->id])
        ->assertSuccessful()
        ->assertSee('Activity description here.')
        ->assertSee('Terms and Conditions');
});

it('displays child dropdown with guardian children', function () {
    $child = Child::factory()->create();
    Relationship::factory()->create([
        'guardian_id' => $this->guardian->id,
        'child_id' => $child->id,
    ]);

    $activity = Activity::factory()->for($this->organization)->create([
        'publish_at' => now(),
    ]);

    Livewire::test(RegisterActivity::class, ['record' => $activity->id])
        ->assertSuccessful()
        ->assertSee('Register for Activity')
        ->assertSee($child->full_name);
});

it('creates a gatepass when registering a child', function () {
    $child = Child::factory()->create();
    Relationship::factory()->create([
        'guardian_id' => $this->guardian->id,
        'child_id' => $child->id,
    ]);

    $activity = Activity::factory()->for($this->organization)->create([
        'publish_at' => now(),
    ]);

    Livewire::test(RegisterActivity::class, ['record' => $activity->id])
        ->set('data.child_id', $child->id)
        ->set('data.guardian_id', $this->guardian->id)
        ->callAction(TestAction::make('register')->schemaComponent('registration-section'))
        ->assertNotified('Successfully registered');

    $this->assertDatabaseHas(Gatepass::class, [
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $this->guardian->id,
    ]);
});

it('shows warning when registering duplicate child-guardian combination', function () {
    $child = Child::factory()->create();
    Relationship::factory()->create([
        'guardian_id' => $this->guardian->id,
        'child_id' => $child->id,
    ]);

    $activity = Activity::factory()->for($this->organization)->create([
        'publish_at' => now(),
    ]);

    Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $this->guardian->id,
    ]);

    Livewire::test(RegisterActivity::class, ['record' => $activity->id])
        ->set('data.child_id', $child->id)
        ->set('data.guardian_id', $this->guardian->id)
        ->callAction(TestAction::make('register')->schemaComponent('registration-section'))
        ->assertNotified('Already registered');

    expect(Gatepass::query()
        ->where('activity_id', $activity->id)
        ->where('child_id', $child->id)
        ->where('guardian_id', $this->guardian->id)
        ->count()
    )->toBe(1);
});

it('displays existing gatepasses in the registered children section', function () {
    $child = Child::factory()->create();
    Relationship::factory()->create([
        'guardian_id' => $this->guardian->id,
        'child_id' => $child->id,
    ]);

    $activity = Activity::factory()->for($this->organization)->create([
        'publish_at' => now(),
    ]);

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $this->guardian->id,
        'code' => 'ABC123',
    ]);

    Livewire::test(RegisterActivity::class, ['record' => $activity->id])
        ->assertSuccessful()
        ->assertSee('Registered Children')
        ->assertSee($child->full_name)
        ->assertSee('ABC123');
});

it('requires terms agreement before registering when activity has terms', function () {
    $child = Child::factory()->create();
    Relationship::factory()->create([
        'guardian_id' => $this->guardian->id,
        'child_id' => $child->id,
    ]);

    $term = Term::factory()->for($this->organization)->published()->create([
        'content' => 'These are the terms and conditions.',
    ]);

    $activity = Activity::factory()->for($this->organization)->create([
        'term_id' => $term->id,
        'publish_at' => now(),
    ]);

    Livewire::test(RegisterActivity::class, ['record' => $activity->id])
        ->set('data.child_id', $child->id)
        ->set('data.guardian_id', $this->guardian->id)
        ->callAction(TestAction::make('register')->schemaComponent('registration-section'))
        ->assertNotified('Agreement Required');

    $this->assertDatabaseMissing(Gatepass::class, [
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $this->guardian->id,
    ]);
});
