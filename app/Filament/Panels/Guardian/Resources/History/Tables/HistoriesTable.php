<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\History\Tables;

use App\Filament\Components\Tables\AppTextColumn;
use App\Models\Attendance;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class HistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                self::activityColumn(),
                AppTextColumn::fullName('child.full_name', 'Child'),
                self::checkedInAtColumn(),
                self::checkedOutAtColumn(),
            ])
            ->recordAction('view')
            ->recordActions([]);
    }

    private static function activityColumn(): TextColumn
    {
        return TextColumn::make('activity.title')
            ->label('Activity')
            ->description(fn (Attendance $record): string => collect([
                $record->activity?->location,
                $record->activity?->organization?->name,
            ])->filter()->implode(' · '))
            ->searchable()
            ->sortable();
    }

    private static function checkedInAtColumn(): TextColumn
    {
        return TextColumn::make('checked_in_at')
            ->label('Checked in')
            ->icon(Heroicon::ArrowRightStartOnRectangle)
            ->iconColor('success')
            ->dateTime('d M Y, g:i A')
            ->sortable()
            ->placeholder('Not checked in');
    }

    private static function checkedOutAtColumn(): TextColumn
    {
        return TextColumn::make('checked_out_at')
            ->label('Checked out')
            ->icon(Heroicon::ArrowRightEndOnRectangle)
            ->iconColor('danger')
            ->dateTime('d M Y, g:i A')
            ->sortable()
            ->placeholder('Not checked out');
    }
}
