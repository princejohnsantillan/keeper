<?php

declare(strict_types=1);

use App\Filament\Panels\Keeper\Resources\Children\ChildResource;
use App\Models\Child;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('keeper'));

    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    Keeper::factory()->for($organization)->for($user)->create();

    $this->actingAs($user);
});

it('has globally searchable attributes that are real database columns', function () {
    $searchableAttributes = ChildResource::getGloballySearchableAttributes();

    expect($searchableAttributes)->toBeArray()
        ->and($searchableAttributes)->toContain('first_name')
        ->and($searchableAttributes)->toContain('last_name')
        ->and($searchableAttributes)->not->toContain('full_name');
});

it('can perform global search without errors', function () {
    Child::factory()->create([
        'first_name' => 'TestChild',
        'last_name' => 'Searchable',
    ]);

    Livewire::test(\Filament\Livewire\GlobalSearch::class)
        ->set('search', 'TestChild')
        ->assertSuccessful();
});
