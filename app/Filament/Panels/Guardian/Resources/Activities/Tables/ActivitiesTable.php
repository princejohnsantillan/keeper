<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Tables;

use App\Filament\Actions\AttendActivityAction;
use App\Filament\Actions\ViewActivityAction;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('starts_at', 'asc')
            ->columns([
                Stack::make([
                    AppSpatieMediaLibraryImageColumn::thumbnail()
                        ->imageHeight(200)
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'rounded-t-xl object-cover w-full']),
                    Stack::make([
                        self::titleColumn(),
                        self::startsAtColumn(),
                        self::locationColumn(),
                    ])->space(2)->extraAttributes(['class' => 'p-4']),
                ]),
            ])
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'lg' => 3,
            ])
            ->recordAction('view_activity')
            ->recordActions([
                ViewActivityAction::make(),
                AttendActivityAction::make(),
            ]);
    }

    private static function titleColumn(): TextColumn
    {
        return TextColumn::make('title')
            ->weight(FontWeight::Bold)
            ->size(TextSize::Large);
    }

    private static function startsAtColumn(): TextColumn
    {
        return TextColumn::make('starts_at')
            ->icon(Heroicon::Calendar)
            ->dateTime('M d, Y \a\t h:i A')
            ->size(TextSize::Small);
    }

    private static function locationColumn(): TextColumn
    {
        return TextColumn::make('location')
            ->icon(Heroicon::MapPin)
            ->size(TextSize::Small)
            ->color('gray');
    }
}
