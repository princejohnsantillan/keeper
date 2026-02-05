<?php

declare(strict_types=1);

use App\Filament\Panels\Guardian\Resources\Guardians\GuardianResource;
use App\Filament\Panels\Guardian\Resources\Guardians\Pages\ViewGuardian;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\Relationship;
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

    Config::set('app.domain', 'keeper.test');

    $this->withServerVariables(['HTTP_HOST' => 'test-org.keeper.test']);

    $this->actingAs($user);

    $this->user = $user;
    $this->guardian = $guardian;
    $this->organization = $organization;
});

it('redirects to index after deleting a guardian from view page', function () {
    $child = Child::factory()->create([
        'owner_id' => $this->user->id,
    ]);

    $guardianToDelete = Guardian::factory()->create([
        'owner_id' => $this->user->id,
    ]);

    Relationship::factory()->create([
        'guardian_id' => $guardianToDelete->id,
        'child_id' => $child->id,
    ]);

    Livewire::test(ViewGuardian::class, ['record' => $guardianToDelete->getRouteKey()])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect(GuardianResource::getUrl('index'));

    $this->assertSoftDeleted(Guardian::class, [
        'id' => $guardianToDelete->id,
    ]);
});
