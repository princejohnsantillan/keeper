<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Invitations\Tables;

use App\Filament\Components\Tables\AppTextColumn;
use App\Models\Invitation;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class InvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                AppTextColumn::code(),
                self::activityColumn(),
                self::inviteeColumn(),
                self::usedByColumn(),
                AppTextColumn::createdAt(),
            ])
            ->filters([
                SelectFilter::make('activity_id')
                    ->label('Activity')
                    ->relationship(
                        'activity',
                        'title',
                        modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_private', true),
                    ),
                TernaryFilter::make('used')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Used')
                    ->falseLabel('Unused')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('used_on_child_id'),
                        false: fn (Builder $query): Builder => $query->whereNull('used_on_child_id'),
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make()
                    ->visible(fn (Invitation $record): bool => ! $record->isUsed()),
            ]);
    }

    private static function activityColumn(): TextColumn
    {
        return TextColumn::make('activity.title')
            ->label('Activity')
            ->searchable()
            ->sortable();
    }

    private static function inviteeColumn(): TextColumn
    {
        return TextColumn::make('invitee_fullname')
            ->label('Invitee')
            ->searchable()
            ->sortable()
            ->description(fn (Invitation $record): string => $record->invitee_email);
    }

    private static function usedByColumn(): TextColumn
    {
        return TextColumn::make('usedOnChild.full_name')
            ->label('Used By')
            ->placeholder('Unused')
            ->badge()
            ->color(fn (Invitation $record): string => $record->isUsed() ? 'success' : 'gray');
    }
}
