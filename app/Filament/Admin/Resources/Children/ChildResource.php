<?php

namespace App\Filament\Admin\Resources\Children;

use App\Filament\Admin\Resources\Children\Pages\CreateChild;
use App\Filament\Admin\Resources\Children\Pages\EditChild;
use App\Filament\Admin\Resources\Children\Pages\ListChildren;
use App\Filament\Admin\Resources\Children\Pages\ViewChild;
use App\Filament\Admin\Resources\Children\Schemas\ChildForm;
use App\Filament\Admin\Resources\Children\Schemas\ChildInfolist;
use App\Filament\Admin\Resources\Children\Tables\ChildrenTable;
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

    protected static ?string $recordTitleAttribute = 'nickname';

    public static function form(Schema $schema): Schema
    {
        return ChildForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChildInfolist::configure($schema);
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
            'view' => ViewChild::route('/{record}'),
            'edit' => EditChild::route('/{record}/edit'),
        ];
    }
}
