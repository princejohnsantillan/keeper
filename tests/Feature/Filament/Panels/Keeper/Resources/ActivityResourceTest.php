<?php

declare(strict_types=1);

use App\Filament\Panels\Keeper\Resources\Activities\Pages\ListActivities;
use App\Models\Keeper;
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
    Livewire::test(ListActivities::class)
        ->assertSuccessful();
});
