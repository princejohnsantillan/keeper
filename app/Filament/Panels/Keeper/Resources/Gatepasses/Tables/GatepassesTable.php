<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Gatepasses\Tables;

use App\Avatar;
use App\Filament\Actions\CheckInGatepassAction;
use App\Filament\Actions\CheckOutGatepassAction;
use App\Filament\Components\Tables\AppTextColumn;
use App\Models\Gatepass;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class GatepassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                AppTextColumn::code(),
                self::activityTitleColumn(),
                self::guardianAvatarColumn(),
                self::guardianGenderColumn(),
                self::guardianFullNameColumn(),
                self::childAvatarColumn(),
                self::childGenderColumn(),
                self::childFullNameColumn(),
            ])
            ->recordActions([
                CheckInGatepassAction::make(),
                CheckOutGatepassAction::make(),
            ]);
    }

    private static function activityTitleColumn(): TextColumn
    {
        return TextColumn::make('activity.title')
            ->searchable()
            ->sortable();
    }

    private static function guardianAvatarColumn(): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make('guardian.avatar')
            ->label('')
            ->collection('avatar')
            ->circular()
            ->defaultImageUrl(fn (Gatepass $record): string => Avatar::generateUrl($record->guardian->full_name));
    }

    private static function guardianGenderColumn(): IconColumn
    {
        return IconColumn::make('guardian.gender')
            ->label('')
            ->boolean();
    }

    private static function guardianFullNameColumn(): TextColumn
    {
        return TextColumn::make('guardian.full_name')
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->whereHas('guardian', function (Builder $query) use ($search): void {
                    $query->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%");
                });
            })
            ->sortable(query: function (Builder $query, string $direction): Builder {
                return $query
                    ->join('guardians', 'gatepasses.guardian_id', '=', 'guardians.id')
                    ->orderBy('guardians.first_name', $direction)
                    ->orderBy('guardians.last_name', $direction)
                    ->select('gatepasses.*');
            });
    }

    private static function childAvatarColumn(): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make('child.avatar')
            ->label('')
            ->collection('avatar')
            ->circular()
            ->defaultImageUrl(fn (Gatepass $record): string => Avatar::generateUrl($record->child->full_name));
    }

    private static function childGenderColumn(): IconColumn
    {
        return IconColumn::make('child.gender')
            ->label('')
            ->boolean();
    }

    private static function childFullNameColumn(): TextColumn
    {
        return TextColumn::make('child.full_name')
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->whereHas('child', function (Builder $query) use ($search): void {
                    $query->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%");
                });
            })
            ->sortable(query: function (Builder $query, string $direction): Builder {
                return $query
                    ->join('children', 'gatepasses.child_id', '=', 'children.id')
                    ->orderBy('children.first_name', $direction)
                    ->orderBy('children.last_name', $direction)
                    ->select('gatepasses.*');
            });
    }
}
