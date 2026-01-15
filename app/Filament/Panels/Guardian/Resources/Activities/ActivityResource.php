<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities;

use App\Filament\Panels\Guardian\Resources\Activities\Pages\AttendActivity;
use App\Filament\Panels\Guardian\Resources\Activities\Pages\ListActivities;
use App\Filament\Panels\Guardian\Resources\Activities\Schemas\ActivityForm;
use App\Filament\Panels\Guardian\Resources\Activities\Tables\ActivitiesTable;
use App\Models\Activity;
use App\Models\Scopes\OrganizationScope;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Play;

    protected static string|UnitEnum|null $navigationGroup = 'Activity';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ActivityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    /**
     * @return Builder<Activity>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(OrganizationScope::class)
            ->where('ends_at', '>=', now())
            ->where('published_at', '<=', now());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'attend' => AttendActivity::route('/{record}/attend'),
        ];
    }
}
