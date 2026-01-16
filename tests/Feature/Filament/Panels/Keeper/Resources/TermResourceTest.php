<?php

declare(strict_types=1);

use App\Filament\Panels\Keeper\Resources\Terms\Pages\ListTerms;
use App\Filament\Panels\Keeper\Resources\Terms\TermResource;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\Term;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
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
    Livewire::test(ListTerms::class)
        ->assertSuccessful();
});

it('can list terms', function () {
    $terms = Term::factory()
        ->for($this->organization)
        ->count(3)
        ->create();

    Livewire::test(ListTerms::class)
        ->assertCanSeeTableRecords($terms);
});

it('can deprecate a term', function () {
    $term = Term::factory()
        ->for($this->organization)
        ->create();

    expect($term->isDeprecated())->toBeFalse();

    Livewire::test(ListTerms::class)
        ->callAction([
            TestAction::make('view')->table($term),
            'deprecate',
        ])
        ->assertNotified();

    expect($term->refresh()->isDeprecated())->toBeTrue();
});

it('hides the deprecate action for already deprecated terms', function () {
    $term = Term::factory()
        ->for($this->organization)
        ->deprecated()
        ->create();

    Livewire::test(ListTerms::class)
        ->assertActionHidden([
            TestAction::make('view')->table($term),
            'deprecate',
        ]);
});

it('hides the edit action for deprecated terms', function () {
    $term = Term::factory()
        ->for($this->organization)
        ->deprecated()
        ->create();

    Livewire::test(ListTerms::class)
        ->assertActionHidden([
            TestAction::make('view')->table($term),
            'edit',
        ]);
});

it('hides the delete action for deprecated terms', function () {
    $term = Term::factory()
        ->for($this->organization)
        ->deprecated()
        ->create();

    Livewire::test(ListTerms::class)
        ->assertActionHidden([
            TestAction::make('view')->table($term),
            'delete',
        ]);
});

it('uses the correct navigation group', function () {
    expect(TermResource::getNavigationGroup())->toBe('Activity');
});
