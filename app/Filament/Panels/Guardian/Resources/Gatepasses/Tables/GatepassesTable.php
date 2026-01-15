<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses\Tables;

use App\Models\Gatepass;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class GatepassesTable
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
                    Split::make([
                        self::childColumn(),
                        self::guardianColumn(),
                    ]),
                    self::codeColumn(),
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
            ->icon(fn (Gatepass $record) => $record->child->gender->getIcon())
            ->iconColor(fn (Gatepass $record) => $record->child->gender->getColor())
            ->getStateUsing(fn (Gatepass $record): string => $record->child->getNickname())
            ->description(fn (Gatepass $record): string => $record->child->full_name);
    }

    private static function guardianColumn(): TextColumn
    {
        return TextColumn::make('guardian.full_name')
            ->label('Guardian')
            ->icon(Heroicon::User)
            ->color('gray');
    }

    private static function codeColumn(): TextColumn
    {
        return TextColumn::make('code')
            ->badge()
            ->color('gray');
    }
}
