<?php

declare(strict_types=1);

use App\Filament\Panels\Keeper\Resources\Messages\MessageResource;
use App\Filament\Panels\Keeper\Resources\Messages\Pages\ListMessages;
use App\Models\Keeper;
use App\Models\Message;
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
    Livewire::test(ListMessages::class)
        ->assertSuccessful();
});

it('can list messages', function () {
    $messages = Message::factory()
        ->for($this->organization)
        ->count(3)
        ->create();

    Livewire::test(ListMessages::class)
        ->assertCanSeeTableRecords($messages);
});

it('can edit a message', function () {
    $message = Message::factory()
        ->for($this->organization)
        ->create();

    Livewire::test(ListMessages::class)
        ->callTableAction('edit', $message, [
            'name' => 'Updated Template Name',
            'content' => '<p>Updated message content.</p>',
        ])
        ->assertNotified();

    expect($message->refresh())
        ->name->toBe('Updated Template Name')
        ->content->toBe('<p>Updated message content.</p>');
});

it('can delete a message', function () {
    $message = Message::factory()
        ->for($this->organization)
        ->create();

    Livewire::test(ListMessages::class)
        ->callTableAction('delete', $message)
        ->assertNotified();

    $this->assertModelMissing($message);
});

it('uses the correct navigation group', function () {
    expect(MessageResource::getNavigationGroup())->toBe('Activity');
});

it('can archive a message', function () {
    $message = Message::factory()
        ->for($this->organization)
        ->create();

    expect($message->isArchived())->toBeFalse();

    Livewire::test(ListMessages::class)
        ->callTableAction('archive', $message)
        ->assertNotified();

    expect($message->refresh()->isArchived())->toBeTrue();
});

it('hides the archive action for already archived messages', function () {
    $message = Message::factory()
        ->for($this->organization)
        ->archived()
        ->create();

    Livewire::test(ListMessages::class)
        ->assertTableActionHidden('archive', $message);
});

it('hides the edit action for archived messages', function () {
    $message = Message::factory()
        ->for($this->organization)
        ->archived()
        ->create();

    Livewire::test(ListMessages::class)
        ->assertTableActionHidden('edit', $message);
});

it('hides the delete action for archived messages', function () {
    $message = Message::factory()
        ->for($this->organization)
        ->archived()
        ->create();

    Livewire::test(ListMessages::class)
        ->assertTableActionHidden('delete', $message);
});
