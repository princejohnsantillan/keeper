<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\InviteKeeperAction as InviteKeeperBusinessAction;
use App\Enums\KeeperRole;
use App\Exceptions\InvitationAlreadyExistsException;
use App\Exceptions\KeeperAlreadyExistsException;
use App\Filament\Notifications\AppNotification;
use App\Subdomain;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;

final class InviteKeeperAction
{
    public static function make(?string $name = 'invite', string $label = 'Invite Keeper'): Action
    {
        return Action::make($name)->label($label)
            ->icon('heroicon-o-envelope')
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Select::make('role')
                    ->label('Role')
                    ->options(KeeperRole::class)
                    ->default(KeeperRole::Gatekeeper)
                    ->required(),
            ])
            ->action(function (array $data, InviteKeeperBusinessAction $inviteKeeper): void {
                $organization = Subdomain::organization();

                if (! $organization) {
                    AppNotification::error('No organization found.')->send();

                    return;
                }

                try {
                    $inviteKeeper(
                        email: $data['email'],
                        name: $data['name'],
                        organization: $organization,
                        invitedBy: Auth::user(),
                        role: $data['role'],
                    );

                    AppNotification::keeperInvited($data['email'])->send();
                } catch (KeeperAlreadyExistsException) {
                    AppNotification::keeperAlreadyExists($data['email'])->send();
                } catch (InvitationAlreadyExistsException) {
                    AppNotification::invitationAlreadyPending($data['email'])->send();
                }
            });
    }
}
