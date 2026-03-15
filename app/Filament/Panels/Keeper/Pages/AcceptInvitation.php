<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Pages;

use App\Actions\AcceptKeeperInvitationAction;
use App\Exceptions\InvalidInvitationException;
use App\Models\KeeperInvitation;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @property-read Schema $form
 */
final class AcceptInvitation extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'invitation/accept';

    protected static string $layout = 'filament-panels::components.layout.simple';

    protected static string|array $withoutRouteMiddleware = [
        Authenticate::class,
        'verified',
    ];

    protected string $view = 'filament-panels::pages.simple';

    public ?KeeperInvitation $invitation = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function isEmailVerificationRequired(Panel $panel): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->normalizeInviteSession();

        $token = request()->query('token');

        if (! $token) {
            Notification::make()
                ->danger()
                ->title('Invalid invitation link')
                ->body('No invitation token was provided.')
                ->persistent()
                ->send();

            $this->redirectRoute('filament.keeper.auth.login');

            return;
        }

        // Load the invitation
        $this->invitation = KeeperInvitation::query()->where('token', $token)->first();

        if (! $this->invitation) {
            Notification::make()
                ->danger()
                ->title('Invalid invitation')
                ->body('The invitation link is invalid or has been removed.')
                ->persistent()
                ->send();

            $this->redirectRoute('filament.keeper.auth.login');

            return;
        }

        if ($this->invitation->isExpired()) {
            Notification::make()
                ->danger()
                ->title('Invitation expired')
                ->body('This invitation has expired. Please contact the person who invited you.')
                ->persistent()
                ->send();

            return;
        }

        if ($this->invitation->isAccepted()) {
            Notification::make()
                ->info()
                ->title('Already accepted')
                ->body('This invitation has already been accepted.')
                ->persistent()
                ->send();

            $this->redirectRoute('filament.keeper.auth.login');

            return;
        }

        // If user already has a password, auto-accept and redirect
        if ($this->invitation->user->password !== null) {
            try {
                app(AcceptKeeperInvitationAction::class)->__invoke($this->invitation->token);

                Notification::make()
                    ->success()
                    ->title('Invitation accepted')
                    ->body('Please log in to access '.$this->invitation->organization->name.'.')
                    ->send();

                $this->redirectRoute('filament.keeper.auth.login');

                return;
            } catch (InvalidInvitationException) {
                // Continue to show error
            }
        }

        $this->form->fill();
    }

    private function normalizeInviteSession(): void
    {
        if (Auth::check()) {
            Auth::logout();
        }

        if (! request()->hasSession()) {
            return;
        }

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->same('password_confirmation')
                    ->revealable(),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->required()
                    ->dehydrated(false)
                    ->revealable(),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('accept')
            ->footer([
                Actions::make($this->getFormActions())
                    ->fullWidth($this->hasFullWidthFormActions()),
            ]);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function accept(): void
    {
        $data = $this->form->getState();

        if (! $this->invitation) {
            return;
        }

        $user = $this->invitation->user;

        // Set password and verify email
        $user->password = Hash::make($data['password']);
        $user->email_verified_at = now();
        $user->save();

        // Accept the invitation and create keeper record
        try {
            app(AcceptKeeperInvitationAction::class)->__invoke($this->invitation->token);

            Auth::login($user);

            Notification::make()
                ->success()
                ->title('Welcome!')
                ->body('You have successfully joined '.$this->invitation->organization->name.'.')
                ->send();

            $this->redirect(Filament::getPanel('keeper')->getUrl());
        } catch (InvalidInvitationException $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function getHeading(): string
    {
        return 'Accept Invitation';
    }

    public function getSubheading(): ?string
    {
        if (! $this->invitation) {
            return null;
        }

        return sprintf(
            'You have been invited to join %s as %s by %s.',
            $this->invitation->organization->name,
            $this->invitation->role->getLabel(),
            $this->invitation->invitedBy->name
        );
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('accept')
                ->label('Accept Invitation & Set Password')
                ->submit('accept'),
        ];
    }

    public function hasLogo(): bool
    {
        return true;
    }
}
