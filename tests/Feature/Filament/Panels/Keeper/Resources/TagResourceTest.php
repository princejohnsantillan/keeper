<?php

declare(strict_types=1);

use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Resources\Tags\Pages\ListTags;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\Tag;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('keeper'));

    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    Keeper::factory()->for($this->organization)->for($this->user)->create();

    Subdomain::fake($this->organization);

    $this->actingAs($this->user);
});

it('can render the list page', function () {
    Livewire::test(ListTags::class)
        ->assertSuccessful();
});

it('can list tags for the organization', function () {
    $tag = Tag::findOrCreateFromString('VIP');

    Livewire::test(ListTags::class)
        ->assertCanSeeTableRecords([$tag]);
});

it('can create a tag', function () {
    Livewire::test(ListTags::class)
        ->callAction('create', ['name' => 'New Tag'])
        ->assertNotified();

    expect(Tag::where('name', 'new tag')->exists())->toBeTrue();
});

it('cannot create a tag with a duplicate name', function () {
    Tag::findOrCreateFromString('Existing');

    Livewire::test(ListTags::class)
        ->callAction('create', ['name' => 'Existing'])
        ->assertHasActionErrors(['name']);
});

it('cannot create a tag with a case-insensitive duplicate name', function () {
    Tag::findOrCreateFromString('existing');

    Livewire::test(ListTags::class)
        ->callAction('create', ['name' => 'EXISTING'])
        ->assertHasActionErrors(['name']);
});

it('can edit a tag', function () {
    $tag = Tag::findOrCreateFromString('Original');

    Livewire::test(ListTags::class)
        ->callTableAction('edit', $tag, ['name' => 'Updated'])
        ->assertNotified();

    expect($tag->fresh()->name)->toBe('updated');
});

it('cannot edit a tag to a duplicate name', function () {
    Tag::findOrCreateFromString('Taken');
    $tag = Tag::findOrCreateFromString('Original');

    Livewire::test(ListTags::class)
        ->callTableAction('edit', $tag, ['name' => 'Taken'])
        ->assertHasTableActionErrors(['name']);
});

it('cannot edit a tag to a case-insensitive duplicate name', function () {
    Tag::findOrCreateFromString('taken');
    $tag = Tag::findOrCreateFromString('Original');

    Livewire::test(ListTags::class)
        ->callTableAction('edit', $tag, ['name' => 'TAKEN'])
        ->assertHasTableActionErrors(['name']);
});
