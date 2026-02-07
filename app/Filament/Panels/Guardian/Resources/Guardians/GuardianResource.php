<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians;

use App\AuthUser;
use App\Filament\Panels\Guardian\Resources\Guardians\Pages\ListGuardians;
use App\Filament\Panels\Guardian\Resources\Guardians\Pages\ViewGuardian;
use App\Filament\Panels\Guardian\Resources\Guardians\Schemas\GuardianForm;
use App\Filament\Panels\Guardian\Resources\Guardians\Schemas\GuardianInfolist;
use App\Filament\Panels\Guardian\Resources\Guardians\Tables\GuardiansTable;
use App\Models\Guardian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

final class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static string|BackedEnum|null $navigationIcon = 'entypo-shield';

    protected static string|UnitEnum|null $navigationGroup = 'Family';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'full_name';

    /**
     * Scope to only the guardians owned by the authenticated user.
     *
     * @return Builder<Guardian>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->ownedBy(AuthUser::user())
            ->with('children');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'phone'];
    }

    /**
     * @param  \App\Models\Guardian  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $phone = $record->phone;
        $birtDate = $record->birth_date;

        $details = [
            'Gender' => $record->gender->getLabel(),
            'Birth date' => $birtDate->format('d M Y').' ('.$birtDate->age.' yrs)',
            'Email' => $record->email,
        ];

        if ($phone !== null) {
            $details['Phone'] = $phone;
        }

        return $details;
    }

    public static function form(Schema $schema): Schema
    {
        return GuardianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuardiansTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GuardianInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuardians::route('/'),
            'view' => ViewGuardian::route('/{record}'),
        ];
    }
}
