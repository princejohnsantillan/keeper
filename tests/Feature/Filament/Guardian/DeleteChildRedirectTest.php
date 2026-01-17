<?php

declare(strict_types=1);

use App\Filament\Panels\Guardian\Resources\Children\ChildResource;
use App\Filament\Panels\Guardian\Resources\Children\Pages\ViewChild;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('guardian'));

    $organization = Organization::factory()->create(['slug' => 'test-org']);
    $guardian = Guardian::factory()->create();
    $user = User::factory()->create(['guardian_id' => $guardian->id]);
    $guardian->organizations()->attach($organization);

    Config::set('app.domain', 'keeper.test');

    $this->withServerVariables(['HTTP_HOST' => 'test-org.keeper.test']);

    $this->actingAs($user);

    $this->user = $user;
    $this->guardian = $guardian;
    $this->organization = $organization;
});

it('redirects to index after deleting a child from view page', function () {
    $child = Child::factory()->create([
        'owner_id' => $this->user->id,
    ]);

    Livewire::test(ViewChild::class, ['record' => $child->getRouteKey()])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect(ChildResource::getUrl('index'));

    $this->assertSoftDeleted(Child::class, [
        'id' => $child->id,
    ]);
});
