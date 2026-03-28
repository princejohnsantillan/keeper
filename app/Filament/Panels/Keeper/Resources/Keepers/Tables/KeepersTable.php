<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Keepers\Tables;

use App\Actions\CancelKeeperInvitationAction;
use App\Enums\KeeperStatus;
use App\Filament\Actions\ChangeKeeperRoleAction as ChangeKeeperRoleFilamentAction;
use App\Filament\Components\Tables\AppTextColumn;
use App\Models\Keeper;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class KeepersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppTextColumn::fullName('user.name'),

                AppTextColumn::email('user.email'),

                TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                AppTextColumn::createdAt(),
            ])
            ->filters([])
            ->recordActions([
                ChangeKeeperRoleFilamentAction::make(),
                self::deactivateAction(),
                self::activateAction(),
                self::cancelInvitationAction(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function deactivateAction(): Action
    {
        return Action::make('deactivate')
            ->label('Deactivate')
            ->icon(Heroicon::OutlinedNoSymbol)
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn (Keeper $record): bool => $record->status === KeeperStatus::Active)
            ->action(function (Keeper $record): void {
                $record->status = KeeperStatus::Inactive;
                $record->save();
            });
    }

    private static function activateAction(): Action
    {
        return Action::make('activate')
            ->label('Activate')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (Keeper $record): bool => $record->status === KeeperStatus::Inactive)
            ->action(function (Keeper $record): void {
                $record->status = KeeperStatus::Active;
                $record->save();
            });
    }

    private static function cancelInvitationAction(): Action
    {
        return Action::make('cancelInvitation')
            ->label('Cancel Invitation')
            ->icon(Heroicon::OutlinedXMark)
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn (Keeper $record): bool => $record->status === KeeperStatus::Pending)
            ->action(function (Keeper $record, CancelKeeperInvitationAction $cancelKeeperInvitation): void {
                $cancelKeeperInvitation($record);
            });
    }
}
