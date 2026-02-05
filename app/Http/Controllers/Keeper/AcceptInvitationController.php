<?php

declare(strict_types=1);

namespace App\Http\Controllers\Keeper;

use App\Actions\AcceptKeeperInvitationAction;
use App\Exceptions\InvalidInvitationException;
use App\Http\Controllers\Controller;
use App\Models\KeeperInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

final class AcceptInvitationController extends Controller
{
    public function show(Request $request): View
    {
        $token = $request->query('token');
        $error = null;
        $invitation = null;
        $showForm = false;

        if (! $token) {
            $error = 'No invitation token was provided.';
        } else {
            $invitation = KeeperInvitation::query()->where('token', $token)->first();

            if (! $invitation) {
                $error = 'The invitation link is invalid or has been removed.';
            } elseif ($invitation->isExpired()) {
                $error = 'This invitation has expired. Please contact the person who invited you.';
            } elseif ($invitation->isAccepted()) {
                $error = 'This invitation has already been accepted. Please log in.';
            } elseif ($invitation->user->password !== null) {
                try {
                    app(AcceptKeeperInvitationAction::class)->__invoke($invitation->token);
                    session()->flash('status', 'Invitation accepted! Please log in.');

                    return view('keeper.accept-invitation-redirect', [ // @phpstan-ignore argument.type
                        'redirectUrl' => route('filament.keeper.auth.login'),
                    ]);
                } catch (InvalidInvitationException) {
                    $error = 'An error occurred while accepting your invitation.';
                }
            } else {
                $showForm = true;
            }
        }

        return view('keeper.accept-invitation', [
            'invitation' => $invitation,
            'error' => $error,
            'showForm' => $showForm,
            'token' => $token,
        ]);
    }

    public function accept(Request $request, AcceptKeeperInvitationAction $acceptInvitation): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'token' => ['required', 'string'],
        ]);

        $invitation = KeeperInvitation::query()->where('token', $request->token)->first();

        if (! $invitation || $invitation->isExpired() || $invitation->isAccepted()) {
            return redirect()->route('filament.keeper.invitation.accept', ['token' => $request->token])
                ->with('error', 'Invalid or expired invitation.');
        }

        $user = $invitation->user;
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now();
        $user->save();

        try {
            $acceptInvitation($request->token);
            Auth::login($user);

            return redirect('/admin')->with('status', 'Welcome! You have successfully joined '.$invitation->organization->name.'.');
        } catch (InvalidInvitationException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
