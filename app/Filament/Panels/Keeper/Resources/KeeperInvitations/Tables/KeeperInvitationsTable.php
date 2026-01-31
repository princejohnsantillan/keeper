<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\KeeperInvitations\Tables;

use App\Filament\Components\Tables\AppTextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class KeeperInvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppTextColumn::fullName()
                    ->state(fn ($record): string => $record->user->name)
                    ->label('Invitee'),

                AppTextColumn::email()
                    ->state(fn ($record): string => $record->user->email),

                TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                TextColumn::make('invitedBy.name')
                    ->label('Invited By')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->state(function ($record): string {
                        if ($record->isExpired()) {
                            return 'Expired';
                        }

                        return 'Pending';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Expired' => 'danger',
                        'Pending' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Cancel Selected'),
                ]),
            ]);
    }
}
