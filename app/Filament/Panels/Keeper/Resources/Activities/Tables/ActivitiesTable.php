<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Tables;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Actions\CloseActivityCheckInAction;
use App\Filament\Actions\OpenActivityCheckInAction;
use App\Filament\Actions\ViewAttendanceAction;
use App\Filament\Actions\WalkInAction;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Filament\Components\Tables\AppTextColumn;
use App\Models\Activity;
use Carbon\CarbonImmutable;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppSpatieMediaLibraryImageColumn::thumbnail(),
                self::titleColumn(),
                AppTextColumn::location(),
                self::startsAtColumn(),
                self::endsAtColumn(),
                self::creatorColumn(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                self::activityActionGroup(),
            ]);
    }

    private static function activityActionGroup(): ActionGroup
    {
        return ActionGroup::make([
            WalkInAction::make(),
            ViewAttendanceAction::make(),
            OpenActivityCheckInAction::make(),
            CloseActivityCheckInAction::make(),
            EditAction::make()
                ->slideOver()
                ->visible(fn (): bool => app(GetCurrentKeeperAction::class)->__invoke()->isAdmin()),
            DeleteAction::make()
                ->visible(fn (): bool => app(GetCurrentKeeperAction::class)->__invoke()->isAdmin()),
        ])
            ->label('Actions')
            ->icon('heroicon-o-ellipsis-horizontal');
    }

    private static function titleColumn(): TextColumn
    {
        return TextColumn::make('title')
            ->description(fn (Activity $activity): ?string => $activity->description)
            ->sortable();
    }

    private static function startsAtColumn(): TextColumn
    {
        return TextColumn::make('starts_at')
            ->dateTime('D - h:i A')
            ->description(function (Activity $record): string {
                /** @var CarbonImmutable $startsAt */
                $startsAt = $record->starts_at->setTimezone(config('app.display_timezone'));

                return $startsAt->format('F d');
            })
            ->sortable();
    }

    private static function endsAtColumn(): TextColumn
    {
        return TextColumn::make('ends_at')
            ->dateTime('D - h:i A')
            ->description(function (Activity $record): string {
                /** @var CarbonImmutable $endsAt */
                $endsAt = $record->ends_at->setTimezone(config('app.display_timezone'));

                return $endsAt->format('F d');
            })
            ->sortable();
    }

    private static function creatorColumn(): TextColumn
    {
        return TextColumn::make('creator.name')
            ->numeric()
            ->sortable();
    }
}
