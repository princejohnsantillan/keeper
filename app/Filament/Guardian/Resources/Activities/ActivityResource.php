<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Activities;

use App\Filament\Guardian\Resources\Activities\Pages\ListActivities;
use App\Filament\Guardian\Resources\Activities\Tables\ActivitiesTable;
use App\Models\Activity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Play;

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}
