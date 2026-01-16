<?php

declare(strict_types=1);

use App\Filament\Panels\Guardian\Resources\Activities\Pages\AttendActivity;
use App\Models\Activity;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\Term;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('guardian'));

    $organization = Organization::factory()->create(['slug' => 'test-org']);
    $user = User::factory()->create();
    $guardian = Guardian::factory()->for($user)->create();
    $guardian->organizations()->attach($organization);

    Config::set('app.domain', 'keeper.test');

    $this->withServerVariables(['HTTP_HOST' => 'test-org.keeper.test']);

    $this->actingAs($user);

    $this->organization = $organization;
});

it('shows description when activity has a description', function () {
    $activity = Activity::factory()->for($this->organization)->create([
        'description' => 'This is a test description for the activity.',
        'published_at' => now(),
    ]);

    Livewire::test(AttendActivity::class, ['record' => $activity->id])
        ->assertSuccessful()
        ->assertSee('This is a test description for the activity.');
});

it('does not show description placeholder when activity has no description', function () {
    $activity = Activity::factory()->for($this->organization)->create([
        'description' => null,
        'published_at' => now(),
    ]);

    Livewire::test(AttendActivity::class, ['record' => $activity->id])
        ->assertSuccessful();
});

it('shows description before terms and conditions', function () {
    $term = Term::factory()->for($this->organization)->published()->create([
        'content' => 'These are the terms and conditions.',
    ]);

    $activity = Activity::factory()->for($this->organization)->create([
        'description' => 'Activity description here.',
        'term_id' => $term->id,
        'published_at' => now(),
    ]);

    Livewire::test(AttendActivity::class, ['record' => $activity->id])
        ->assertSuccessful()
        ->assertSee('Activity description here.')
        ->assertSee('Terms and Conditions');
});
