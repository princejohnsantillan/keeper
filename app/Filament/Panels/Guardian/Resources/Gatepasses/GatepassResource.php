<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses;

use App\AuthUser;
use App\Filament\Panels\Guardian\Resources\Gatepasses\Pages\ListGatepasses;
use App\Filament\Panels\Guardian\Resources\Gatepasses\Pages\ViewGatepass;
use App\Filament\Panels\Guardian\Resources\Gatepasses\Schemas\GatepassForm;
use App\Filament\Panels\Guardian\Resources\Gatepasses\Schemas\GatepassInfolist;
use App\Filament\Panels\Guardian\Resources\Gatepasses\Tables\GatepassesTable;
use App\Models\Gatepass;
use App\Models\Guardian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class GatepassResource extends Resource
{
    protected static ?string $model = Gatepass::class;

    protected static string|BackedEnum|null $navigationIcon = 'entypo-lock';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Scope to only the gatepasses associated to the guardians.
     *
     * @return Builder<Gatepass>
     */
    public static function getEloquentQuery(): Builder
    {
        $guardians = Guardian::query()
            ->whereHas('relationships', function (Builder $query): void {
                $query->whereIn('child_id', AuthUser::guardian()->children()->pluck('children.id'));
            });

        /** @var Builder<Gatepass> $query */
        $query = parent::getEloquentQuery()
            ->whereIn('guardian_id', $guardians->pluck('guardians.id'));

        return $query;
    }

    /**
     * @param  \App\Models\Gatepass  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Activity' => $record->activity->title,
            'Guardian' => $record->guardian->full_name,
            'Child' => $record->child->full_name,
        ];
    }

    /**
     * @return Builder<Gatepass>
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        /** @var Builder<Gatepass> $query */
        $query = parent::getGlobalSearchEloquentQuery()->with(['activity', 'guardian', 'child']);

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return GatepassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GatepassesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GatepassInfolist::configure($schema);
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
            'index' => ListGatepasses::route('/'),
            'view' => ViewGatepass::route('/{record}'),
        ];
    }
}
