<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children;

use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Resources\Children\Pages\ListChildren;
use App\Filament\Panels\Keeper\Resources\Children\Pages\ViewChild;
use App\Filament\Panels\Keeper\Resources\Children\Schemas\ChildForm;
use App\Filament\Panels\Keeper\Resources\Children\Schemas\ChildInfolist;
use App\Filament\Panels\Keeper\Resources\Children\Tables\ChildrenTable;
use App\Models\Child;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class ChildResource extends Resource
{
    protected static ?string $model = Child::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-children';

    protected static string|UnitEnum|null $navigationGroup = 'People';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'middle_name', 'last_name', 'nickname'];
    }

    /** @return Builder<Child> */
    public static function getEloquentQuery(): Builder
    {
        $organization = Subdomain::organization();

        return parent::getEloquentQuery()
            ->whereHas('gatepasses.activity', function (Builder $query) use ($organization): void {
                $query->where('organization_id', $organization?->id);
            });
    }

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
            'view' => ViewChild::route('/{record}'),
        ];
    }
}
