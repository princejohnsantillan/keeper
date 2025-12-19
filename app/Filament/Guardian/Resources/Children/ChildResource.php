<?php

namespace App\Filament\Guardian\Resources\Children;

use App\Filament\Guardian\Resources\Children\Pages\ListChildren;
use App\Filament\Guardian\Resources\Children\Schemas\ChildForm;
use App\Filament\Guardian\Resources\Children\Tables\ChildrenTable;
use App\Models\Child;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ChildResource extends Resource
{
    protected static ?string $model = Child::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-children';

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->whereHas('relationships', function (Builder $query) use ($user): void {
                $query->where('guardian_id', $user->guardian->id);
            });
    }

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
        ];
    }
}
