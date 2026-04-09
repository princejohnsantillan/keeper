<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Tables;

use App\Filament\Actions\RegisterActivityAction;
use App\Filament\Actions\ViewActivityAction;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Models\Activity;
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
                        ->width('100%')
                        ->imageWidth('100%')
                        ->imageHeight('12rem')
                        ->extraImgAttributes(['class' => 'rounded-t-xl bg-gray-50 object-cover object-center w-full']),
                    Stack::make([
                        self::titleColumn(),
                        self::startsAtColumn(),
                        self::locationColumn(),
                    ])->space(2)->extraAttributes(['class' => 'p-4']),
                ]),
            ])
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->recordAction('view_activity')
            ->recordActions([
                ViewActivityAction::make(),
                RegisterActivityAction::make(),
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
            ->formatStateUsing(function (mixed $state, Activity $record): string {
                return $record->starts_at
                    ->setTimezone(config('app.display_timezone'))
                    ->format('M d, Y \a\t h:i A');
            })
            ->description(function (Activity $record): ?string {
                $displayTimezone = config('app.display_timezone');
                $startsAt = $record->starts_at->setTimezone($displayTimezone);
                $endsAt = $record->ends_at->setTimezone($displayTimezone);

                if ($startsAt->isSameDay($endsAt)) {
                    return null;
                }

                return 'Ends '.$endsAt->format('M d, Y \a\t h:i A');
            })
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
