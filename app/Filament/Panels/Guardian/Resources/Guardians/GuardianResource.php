<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians;

use App\AuthUser;
use App\Filament\Panels\Guardian\Resources\Guardians\Pages\ListGuardians;
use App\Filament\Panels\Guardian\Resources\Guardians\Schemas\GuardianForm;
use App\Filament\Panels\Guardian\Resources\Guardians\Tables\GuardiansTable;
use App\Models\Guardian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static string|BackedEnum|null $navigationIcon = 'entypo-shield';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'full_name';

    /**
     * Scope to only the guardians of the authenticated user's children.
     *
     * @return Builder<Guardian>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('relationships', function (Builder $query): void {
                $query->whereIn('child_id', AuthUser::guardian()->children()->pluck('children.id'));
            });
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuardians::route('/'),
        ];
    }
}
