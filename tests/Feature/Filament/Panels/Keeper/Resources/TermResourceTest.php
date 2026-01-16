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

it('can archive a term', function () {
    $term = Term::factory()
        ->for($this->organization)
        ->create();

    expect($term->isArchived())->toBeFalse();

    Livewire::test(ListTerms::class)
        ->callAction([
            TestAction::make('view')->table($term),
            'archive',
        ])
        ->assertNotified();

    expect($term->refresh()->isArchived())->toBeTrue();
});

it('hides the archive action for already archived terms', function () {
    $term = Term::factory()
        ->for($this->organization)
        ->archived()
        ->create();

    Livewire::test(ListTerms::class)
        ->assertActionHidden([
            TestAction::make('view')->table($term),
            'archive',
        ]);
});

it('hides the edit action for archived terms', function () {
    $term = Term::factory()
        ->for($this->organization)
        ->archived()
        ->create();

    Livewire::test(ListTerms::class)
        ->assertActionHidden([
            TestAction::make('view')->table($term),
            'edit',
        ]);
});

it('hides the delete action for archived terms', function () {
    $term = Term::factory()
        ->for($this->organization)
        ->archived()
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
