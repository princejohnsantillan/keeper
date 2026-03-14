<?php

declare(strict_types=1);

use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Pages\AcceptInvitation;
use App\Models\KeeperInvitation;
use App\Models\Organization;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->organization = Organization::factory()->create();
    Subdomain::fake($this->organization);
    Filament::setCurrentPanel(Filament::getPanel('keeper'));
    Filament::bootCurrentPanel();
});

it('redirects to login with notification when no token is provided', function (): void {
    Livewire::withQueryParams([])
        ->test(AcceptInvitation::class)
        ->assertRedirect(route('filament.keeper.auth.login'));
});

it('redirects to login with notification for invalid token', function (): void {
    Livewire::withQueryParams(['token' => Str::random(32)])
        ->test(AcceptInvitation::class)
        ->assertRedirect(route('filament.keeper.auth.login'));
});

it('shows expired message for expired invitation', function (): void {
    $invitation = KeeperInvitation::factory()
        ->expired()
        ->create([
            'organization_id' => $this->organization->id,
        ]);

    $test = Livewire::withQueryParams(['token' => $invitation->token])
        ->test(AcceptInvitation::class);

    $test->assertOk();
    $test->assertNoRedirect();
    $test->assertSee('Accept Invitation');
});

it('redirects to login when invitation is already accepted', function (): void {
    $invitation = KeeperInvitation::factory()
        ->accepted()
        ->create([
            'organization_id' => $this->organization->id,
        ]);

    Livewire::withQueryParams(['token' => $invitation->token])
        ->test(AcceptInvitation::class)
        ->assertRedirect(route('filament.keeper.auth.login'));
});

it('auto-accepts and redirects to login when user already has a password', function (): void {
    $invitation = KeeperInvitation::factory()
        ->withPendingKeeper()
        ->create([
            'organization_id' => $this->organization->id,
        ]);
    $invitation->user->update(['password' => bcrypt('existing-password')]);

    Livewire::withQueryParams(['token' => $invitation->token])
        ->test(AcceptInvitation::class)
        ->assertRedirect(route('filament.keeper.auth.login'));
});

it('renders password form for valid invitation when user has no password', function (): void {
    $invitation = KeeperInvitation::factory()
        ->withPendingKeeper()
        ->create([
            'organization_id' => $this->organization->id,
        ]);

    Livewire::withQueryParams(['token' => $invitation->token])
        ->test(AcceptInvitation::class)
        ->assertOk()
        ->assertSee('Accept Invitation')
        ->assertSee('Password')
        ->assertSee('Confirm Password')
        ->assertSee('Accept Invitation & Set Password');
});

it('accepts invitation and sets password on form submission', function (): void {
    $invitation = KeeperInvitation::factory()
        ->withPendingKeeper()
        ->create([
            'organization_id' => $this->organization->id,
        ]);
    $user = $invitation->user;

    Livewire::withQueryParams(['token' => $invitation->token])
        ->test(AcceptInvitation::class)
        ->fillForm([
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->call('accept')
        ->assertNotified()
        ->assertRedirect();

    $user->refresh();
    expect($user->password)->not->toBeNull()
        ->and($user->email_verified_at)->not->toBeNull();

    $invitation->refresh();
    expect($invitation->accepted_at)->not->toBeNull();
});

it('validates password confirmation matches', function (): void {
    $invitation = KeeperInvitation::factory()
        ->withPendingKeeper()
        ->create([
            'organization_id' => $this->organization->id,
        ]);

    Livewire::withQueryParams(['token' => $invitation->token])
        ->test(AcceptInvitation::class)
        ->fillForm([
            'password' => 'new-secure-password',
            'password_confirmation' => 'different-password',
        ])
        ->call('accept')
        ->assertHasFormErrors(['password'])
        ->assertNotNotified();
});
