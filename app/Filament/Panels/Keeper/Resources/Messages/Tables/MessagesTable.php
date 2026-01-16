<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Messages\Tables;

use App\Filament\Actions\ArchiveMessageAction;
use App\Filament\Components\Tables\AppTextColumn;
use App\Models\Message;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppTextColumn::name(),
                self::archivedAtColumn(),
                self::createdAtColumn(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->hidden(fn (Message $record): bool => $record->isArchived()),
                ArchiveMessageAction::make(),
                DeleteAction::make()
                    ->hidden(fn (Message $record): bool => $record->isArchived()),
            ]);
    }

    private static function archivedAtColumn(): TextColumn
    {
        return TextColumn::make('archived_at')
            ->label('Status')
            ->badge()
            ->formatStateUsing(fn (?string $state): string => $state ? 'Archived' : 'Active')
            ->color(fn (?string $state): string => $state ? 'danger' : 'success');
    }

    private static function createdAtColumn(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label('Created')
            ->dateTime('M d, Y')
            ->sortable();
    }
}
