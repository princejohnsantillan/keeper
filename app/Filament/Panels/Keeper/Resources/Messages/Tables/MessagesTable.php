<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Messages\Tables;

use App\Filament\Components\Tables\AppTextColumn;
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
                self::createdAtColumn(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
            ]);
    }

    private static function createdAtColumn(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label('Created')
            ->dateTime('M d, Y')
            ->sortable();
    }
}
