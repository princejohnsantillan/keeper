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
