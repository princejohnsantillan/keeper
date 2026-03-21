<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Gatepasses;

use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Resources\Gatepasses\Pages\ListGatepasses;
use App\Filament\Panels\Keeper\Resources\Gatepasses\Schemas\GatepassForm;
use App\Filament\Panels\Keeper\Resources\Gatepasses\Tables\GatepassesTable;
use App\Models\Gatepass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class GatepassResource extends Resource
{
    protected static ?string $model = Gatepass::class;

    protected static string|BackedEnum|null $navigationIcon = 'entypo-lock';

    protected static string|UnitEnum|null $navigationGroup = 'Activity';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Gate Passes';

    protected static ?string $modelLabel = 'Gate Passes';

    protected static ?string $pluralModelLabel = 'Gate Passes';

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return GatepassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GatepassesTable::configure($table);
    }

    /** @return Builder<Gatepass> */
    public static function getEloquentQuery(): Builder
    {
        $organization = Subdomain::organization();

        if ($organization === null) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('organization_id', $organization->id)
            ->whereHas('activity')
            ->with(['activity', 'guardian', 'child']);
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
        ];
    }
}
