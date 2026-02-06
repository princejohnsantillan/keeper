<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\ChangeKeeperRoleAction as ChangeKeeperRoleBusinessAction;
use App\Actions\GetCurrentKeeperAction;
use App\Enums\KeeperRole;
use App\Enums\KeeperStatus;
use App\Exceptions\CannotChangeOwnAdminRoleException;
use App\Exceptions\CannotDemoteLastAdminException;
use App\Filament\Notifications\AppNotification;
use App\Models\Keeper;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Auth\Access\AuthorizationException;

final class ChangeKeeperRoleAction
{
    public static function make(?string $name = 'change_role', string $label = 'Change role'): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon(Heroicon::OutlinedShieldCheck)
            ->visible(fn (Keeper $record): bool => self::isVisibleFor($record))
            ->modalHeading(fn (Keeper $record): string => 'Change role for '.$record->user->name)
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn (Keeper $record): array => [
                'role' => $record->role?->value,
            ])
            ->schema([
                self::roleField()
                    ->columnSpanFull(),
            ])
            ->action(function (
                Keeper $record,
                array $data,
                GetCurrentKeeperAction $getCurrentKeeper,
                ChangeKeeperRoleBusinessAction $changeKeeperRole,
            ): void {
                try {
                    $actingKeeper = $getCurrentKeeper->__invoke();
                    $keeper = $changeKeeperRole(
                        keeper: $record,
                        newRole: $data['role'],
                        actingKeeper: $actingKeeper,
                    );

                    AppNotification::keeperRoleUpdated($keeper->user->name, $keeper->role)->send();
                } catch (
                    AuthorizationException
                    | CannotDemoteLastAdminException
                    | CannotChangeOwnAdminRoleException $exception
                ) {
                    AppNotification::error($exception->getMessage())->send();
                }
            });
    }

    private static function isVisibleFor(Keeper $record): bool
    {
        $currentKeeper = app(GetCurrentKeeperAction::class)->__invoke();

        return in_array($record->status, [KeeperStatus::Active, KeeperStatus::Inactive], true)
            && $record->id !== $currentKeeper->id;
    }

    private static function roleField(): Select
    {
        return Select::make('role')
            ->label('Role')
            ->options(KeeperRole::class)
            ->selectablePlaceholder(false)
            ->required();
    }
}
