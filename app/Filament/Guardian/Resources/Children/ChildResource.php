<?php

declare(strict_types=1);

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

final class ChildResource extends Resource
{
    protected static ?string $model = Child::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-children';

    /** @return Builder<Child> */
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
