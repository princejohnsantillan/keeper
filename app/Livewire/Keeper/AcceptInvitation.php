<?php

declare(strict_types=1);

namespace App\Livewire\Keeper;

use App\Actions\AcceptKeeperInvitationAction;
use App\Exceptions\InvalidInvitationException;
use App\Models\KeeperInvitation;
use App\Subdomain;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

final class AcceptInvitation extends Component
{
    public ?KeeperInvitation $invitation = null;

    public string $password = '';

    public string $password_confirmation = '';

    public bool $showForm = false;

    public string $error = '';

    public function mount()
    {
        $token = request()->query('token');

        if (! $token) {
            $this->error = 'No invitation token was provided.';

            return;
        }

        $this->invitation = KeeperInvitation::query()->where('token', $token)->first();

        if (! $this->invitation) {
            $this->error = 'The invitation link is invalid or has been removed.';

            return;
        }

        if ($this->invitation->isExpired()) {
            $this->error = 'This invitation has expired. Please contact the person who invited you.';

            return;
        }

        if ($this->invitation->isAccepted()) {
            $this->error = 'This invitation has already been accepted. Please log in.';

            return;
        }

        // If user already has a password, auto-accept and redirect
        if ($this->invitation->user->password !== null) {
            try {
                app(AcceptKeeperInvitationAction::class)->__invoke($this->invitation->token);

                session()->flash('message', 'Invitation accepted! Please log in to access '.$this->invitation->organization->name.'.');

                return $this->redirect(route('filament.keeper.auth.login'));
            } catch (InvalidInvitationException) {
                $this->error = 'An error occurred while accepting your invitation.';

                return;
            }
        }

        $this->showForm = true;
    }

    public function accept()
    {
        $this->validate([
            'password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
            'password_confirmation' => ['required'],
        ]);

        if (! $this->invitation) {
            return;
        }

        $user = $this->invitation->user;

        // Set password and verify email
        $user->password = Hash::make($this->password);
        $user->email_verified_at = now();
        $user->save();

        // Accept the invitation and create keeper record
        try {
            app(AcceptKeeperInvitationAction::class)->__invoke($this->invitation->token);

            Auth::login($user);

            session()->flash('message', 'Welcome! You have successfully joined '.$this->invitation->organization->name.'.');

            return $this->redirect('/admin');
        } catch (InvalidInvitationException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.keeper.accept-invitation', [
            'organizationName' => $this->invitation?->organization->name ?? Subdomain::organization()?->name,
        ]);
    }
}
