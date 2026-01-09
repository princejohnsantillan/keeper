<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Terms;

use App\Filament\Panels\Keeper\Resources\Terms\Pages\ListTerms;
use App\Filament\Panels\Keeper\Resources\Terms\Schemas\TermForm;
use App\Filament\Panels\Keeper\Resources\Terms\Schemas\TermInfolist;
use App\Filament\Panels\Keeper\Resources\Terms\Tables\TermsTable;
use App\Models\Term;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Terms & Conditions';

    protected static ?string $modelLabel = 'Terms & Conditions';

    protected static ?string $pluralModelLabel = 'Terms & Conditions';

    public static function form(Schema $schema): Schema
    {
        return TermForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TermInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TermsTable::configure($table);
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
            'index' => ListTerms::route('/'),
        ];
    }
}
