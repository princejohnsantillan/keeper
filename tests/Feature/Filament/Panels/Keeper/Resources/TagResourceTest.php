<?php

declare(strict_types=1);

use App\Filament\Panels\Keeper\Resources\Tags\Pages\ListTags;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\Tag;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Once;
use Livewire\Livewire;

beforeEach(function () {
    Once::flush();

    Filament::setCurrentPanel(Filament::getPanel('keeper'));

    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    Keeper::factory()->for($this->organization)->for($this->user)->create();

    $host = $this->organization->slug.'.'.Config::string('app.domain');
    request()->headers->set('Host', $host);

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
