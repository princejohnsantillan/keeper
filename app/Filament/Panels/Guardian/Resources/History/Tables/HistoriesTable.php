<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\History\Tables;

use App\Models\Attendance;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class HistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('created_at', 'desc')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    self::activityTitleColumn(),
                    self::childColumn(),
                    Split::make([
                        self::checkedInAtColumn(),
                        self::checkedOutAtColumn(),
                    ]),
                ])->space(2),
            ])
            ->recordAction('view')
            ->recordActions([]);
    }

    private static function activityTitleColumn(): TextColumn
    {
        return TextColumn::make('activity.title')
            ->weight(FontWeight::Bold)
            ->size(TextSize::Large);
    }

    private static function childColumn(): TextColumn
    {
        return TextColumn::make('child.full_name')
            ->label('Child')
            ->icon(fn (Attendance $record) => $record->child->gender->getIcon())
            ->iconColor(fn (Attendance $record) => $record->child->gender->getColor())
            ->getStateUsing(fn (Attendance $record): string => $record->child->getNickname())
            ->description(fn (Attendance $record): string => $record->child->full_name);
    }

    private static function checkedInAtColumn(): TextColumn
    {
        return TextColumn::make('checked_in_at')
            ->label('Checked in')
            ->icon(Heroicon::ArrowRightStartOnRectangle)
            ->iconColor('success')
            ->dateTime('d M Y, g:i A')
            ->placeholder('Not checked in');
    }

    private static function checkedOutAtColumn(): TextColumn
    {
        return TextColumn::make('checked_out_at')
            ->label('Checked out')
            ->icon(Heroicon::ArrowRightEndOnRectangle)
            ->iconColor('danger')
            ->dateTime('d M Y, g:i A')
            ->placeholder('Not checked out');
    }
}
