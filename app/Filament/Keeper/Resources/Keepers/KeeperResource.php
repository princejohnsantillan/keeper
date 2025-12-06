<?php

namespace App\Filament\Keeper\Resources\Keepers;

use App\Filament\Keeper\Resources\Keepers\Pages\CreateKeeper;
use App\Filament\Keeper\Resources\Keepers\Pages\EditKeeper;
use App\Filament\Keeper\Resources\Keepers\Pages\ListKeepers;
use App\Filament\Keeper\Resources\Keepers\Pages\ViewKeeper;
use App\Filament\Keeper\Resources\Keepers\Schemas\KeeperForm;
use App\Filament\Keeper\Resources\Keepers\Schemas\KeeperInfolist;
use App\Filament\Keeper\Resources\Keepers\Tables\KeepersTable;
use App\Models\Keeper;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KeeperResource extends Resource
{
    protected static ?string $model = Keeper::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return KeeperForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KeeperInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KeepersTable::configure($table);
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
            'index' => ListKeepers::route('/'),
            'create' => CreateKeeper::route('/create'),
            'view' => ViewKeeper::route('/{record}'),
            'edit' => EditKeeper::route('/{record}/edit'),
        ];
    }
}
