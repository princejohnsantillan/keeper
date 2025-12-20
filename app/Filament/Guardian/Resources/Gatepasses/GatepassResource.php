<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Gatepasses;

use App\AuthUser;
use App\Filament\Guardian\Resources\Gatepasses\Pages\ListGatepasses;
use App\Filament\Guardian\Resources\Gatepasses\Schemas\GatepassForm;
use App\Filament\Guardian\Resources\Gatepasses\Tables\GatepassesTable;
use App\Models\Gatepass;
use App\Models\Guardian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class GatepassResource extends Resource
{
    protected static ?string $model = Gatepass::class;

    protected static string|BackedEnum|null $navigationIcon = 'entypo-lock';

    protected static ?int $navigationSort = 4;

    /**
     * Scope to only the gatepasses associated to the guardians.
     *
     * @return Builder<Gatepass>
     */
    public static function getEloquentQuery(): Builder
    {
        $guardians = Guardian::query()
            ->whereHas('relationships', function (Builder $query): void {
                $query->whereIn('child_id', AuthUser::guardian()->children()->pluck('children.id'));
            });

        return parent::getEloquentQuery()
            ->whereIn('guardian_id', $guardians->pluck('guardians.id'));
    }

    public static function form(Schema $schema): Schema
    {
        return GatepassForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GatepassesTable::configure($table);
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
            'index' => ListGatepasses::route('/'),
        ];
    }
}
