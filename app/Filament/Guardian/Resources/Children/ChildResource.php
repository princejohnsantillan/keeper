<?php

namespace App\Filament\Guardian\Resources\Children;

use App\Filament\Guardian\Resources\Children\Pages\CreateChild;
use App\Filament\Guardian\Resources\Children\Pages\EditChild;
use App\Filament\Guardian\Resources\Children\Pages\ListChildren;
use App\Filament\Guardian\Resources\Children\Schemas\ChildForm;
use App\Filament\Guardian\Resources\Children\Tables\ChildrenTable;
use App\Models\Child;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChildResource extends Resource
{
    protected static ?string $model = Child::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ChildForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChildrenTable::configure($table);
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
            'index' => ListChildren::route('/'),
            'create' => CreateChild::route('/create'),
            'edit' => EditChild::route('/{record}/edit'),
        ];
    }
}
