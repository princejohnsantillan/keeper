<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Children;

use App\Filament\Guardian\Resources\Children\Pages\ListChildren;
use App\Filament\Guardian\Resources\Children\Pages\ViewChild;
use App\Filament\Guardian\Resources\Children\Schemas\ChildForm;
use App\Filament\Guardian\Resources\Children\Schemas\ChildInfolist;
use App\Filament\Guardian\Resources\Children\Tables\ChildrenTable;
use App\Models\Child;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class ChildResource extends Resource
{
    protected static ?string $model = Child::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-children';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'full_name';

    /**
     * Scope to only the children whose guardian is the authenticated user.
     *
     * @return Builder<Child>
     */
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        $guardian = $user?->guardian;

        abort_if($guardian === null, 403);

        return parent::getEloquentQuery()
            ->whereHas('relationships', function (Builder $query) use ($guardian): void {
                $query->where('guardian_id', $guardian->id);
            });
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
            'Nickname' => $record->nickname ?? Str::before($record->first_name, ' '),
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
