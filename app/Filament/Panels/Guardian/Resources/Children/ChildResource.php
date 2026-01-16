<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children;

use App\AuthUser;
use App\Filament\Panels\Guardian\Resources\Children\Pages\ListChildren;
use App\Filament\Panels\Guardian\Resources\Children\Pages\ViewChild;
use App\Filament\Panels\Guardian\Resources\Children\Schemas\ChildForm;
use App\Filament\Panels\Guardian\Resources\Children\Schemas\ChildInfolist;
use App\Filament\Panels\Guardian\Resources\Children\Tables\ChildrenTable;
use App\Models\Child;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

final class ChildResource extends Resource
{
    protected static ?string $model = Child::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-children';

    protected static string|UnitEnum|null $navigationGroup = 'Family';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

    /**
     * Scope to only the children owned by the authenticated user.
     *
     * @return Builder<Child>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('owner_id', AuthUser::user()->id)
            ->with('guardians');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'nickname'];
    }

    /**
     * @param  Child  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Gender' => $record->gender->getLabel(),
            'Known As' => $record->known_as,
            'Birth date' => $record->birth_date->format('d M Y').' ('.$record->birth_date->age.' yrs)',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ChildForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChildrenTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChildInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChildren::route('/'),
            'view' => ViewChild::route('/{record}'),
        ];
    }
}
