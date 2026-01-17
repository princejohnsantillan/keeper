<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\History;

use App\AuthUser;
use App\Filament\Panels\Guardian\Resources\History\Pages\ListHistory;
use App\Filament\Panels\Guardian\Resources\History\Pages\ViewHistory;
use App\Filament\Panels\Guardian\Resources\History\Schemas\HistoryInfolist;
use App\Filament\Panels\Guardian\Resources\History\Tables\HistoriesTable;
use App\Models\Attendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

final class HistoryResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $modelLabel = 'History';

    protected static ?string $pluralModelLabel = 'History';

    protected static ?string $slug = 'history';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    protected static string|UnitEnum|null $navigationGroup = 'Activity';

    protected static ?int $navigationSort = 3;

    /**
     * Scope to only the attendance records for the guardian's children.
     *
     * @return Builder<Attendance>
     */
    public static function getEloquentQuery(): Builder
    {
        $childIds = AuthUser::guardian()->children()->pluck('children.id');

        /** @var Builder<Attendance> $query */
        $query = parent::getEloquentQuery()
            ->whereIn('child_id', $childIds)
            ->with([
                'activity.organization',
                'child',
                'checkinGatepass.guardian',
                'checkoutGatepass.guardian',
                'checkinKeeper.user',
                'checkoutKeeper.user',
            ]);

        return $query;
    }

    /**
     * @param  Attendance  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Activity' => $record->activity->title,
            'Child' => $record->child->full_name,
        ];
    }

    /**
     * @return Builder<Attendance>
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        /** @var Builder<Attendance> $query */
        $query = parent::getGlobalSearchEloquentQuery()->with(['activity', 'child']);

        return $query;
    }

    public static function table(Table $table): Table
    {
        return HistoriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HistoryInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHistory::route('/'),
            'view' => ViewHistory::route('/{record}'),
        ];
    }
}
