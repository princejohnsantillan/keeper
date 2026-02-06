<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Keepers;

use App\Actions\GetCurrentKeeperAction;
use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Resources\Keepers\Pages\ListKeepers;
use App\Filament\Panels\Keeper\Resources\Keepers\Tables\KeepersTable;
use App\Models\Keeper;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

final class KeeperResource extends Resource
{
    protected static ?string $model = Keeper::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Team';

    protected static UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        $currentKeeper = app(GetCurrentKeeperAction::class)->__invoke();

        return $currentKeeper->isAdmin();
    }

    /** @return Builder<Keeper> */
    public static function getEloquentQuery(): Builder
    {
        $organization = Subdomain::organization();

        return parent::getEloquentQuery()
            ->where('organization_id', $organization?->id)
            ->with(['user']);
    }

    public static function table(Table $table): Table
    {
        return KeepersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKeepers::route('/'),
        ];
    }

    /** @return Builder<Keeper> */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
