<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Tags;

use App\Filament\Panels\Keeper\Resources\Tags\Pages\CreateTag;
use App\Filament\Panels\Keeper\Resources\Tags\Pages\EditTag;
use App\Filament\Panels\Keeper\Resources\Tags\Pages\ListTags;
use App\Filament\Panels\Keeper\Resources\Tags\Schemas\TagForm;
use App\Filament\Panels\Keeper\Resources\Tags\Tables\TagsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Tags\Tag;

final class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
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
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
