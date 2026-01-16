<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Messages\Tables;

use App\Filament\Actions\DeprecateMessageAction;
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
                self::deprecatedAtColumn(),
                self::createdAtColumn(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->recordActions([
                EditAction::make()
                    ->slideOver()
                    ->hidden(fn (Message $record): bool => $record->isDeprecated()),
                DeprecateMessageAction::make(),
                DeleteAction::make()
                    ->hidden(fn (Message $record): bool => $record->isDeprecated()),
            ]);
    }

    private static function deprecatedAtColumn(): TextColumn
    {
        return TextColumn::make('deprecated_at')
            ->label('Status')
            ->badge()
            ->formatStateUsing(fn (?string $state): string => $state ? 'Deprecated' : 'Active')
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
