<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Keepers\Tables;

use App\Enums\KeeperStatus;
use App\Filament\Components\Tables\AppTextColumn;
use App\Models\Keeper;
use App\Models\KeeperInvitation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class KeepersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppTextColumn::fullName()
                    ->state(fn ($record): string => $record->user->name),

                AppTextColumn::email()
                    ->state(fn ($record): string => $record->user->email),

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
                EditAction::make()
                    ->visible(fn (Keeper $record): bool => $record->status !== KeeperStatus::Pending),
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
            ->action(function (Keeper $record): void {
                // Delete the associated invitation if it exists
                KeeperInvitation::query()
                    ->where('user_id', $record->user_id)
                    ->where('organization_id', $record->organization_id)
                    ->delete();

                $record->delete();
            });
    }
}
