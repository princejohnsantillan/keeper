<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Gatepasses;

use App\Filament\Guardian\Resources\Gatepasses\Pages\ListGatepasses;
use App\Filament\Guardian\Resources\Gatepasses\Schemas\GatepassForm;
use App\Filament\Guardian\Resources\Gatepasses\Tables\GatepassesTable;
use App\Models\Gatepass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

final class GatepassResource extends Resource
{
    protected static ?string $model = Gatepass::class;

    protected static string|BackedEnum|null $navigationIcon = 'entypo-lock';

    public static function form(Schema $schema): Schema
    {
        return GatepassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GatepassesTable::configure($table);
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
