<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Guardians;

use App\Actions\GetCurrentKeeperAction;
use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Resources\Guardians\Pages\ListGuardians;
use App\Filament\Panels\Keeper\Resources\Guardians\Schemas\GuardianForm;
use App\Filament\Panels\Keeper\Resources\Guardians\Tables\GuardiansTable;
use App\Models\Guardian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static string|BackedEnum|null $navigationIcon = 'entypo-shield';

    protected static string|UnitEnum|null $navigationGroup = 'People';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function canAccess(): bool
    {
        $currentKeeper = app(GetCurrentKeeperAction::class)->__invoke();

        return $currentKeeper->isAdmin();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'phone'];
    }

    /** @return Builder<Guardian> */
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
        return GuardianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuardiansTable::configure($table);
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
            'index' => ListGuardians::route('/'),
        ];
    }
}
